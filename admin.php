<?php
session_start();
include 'config/db.php';

// Only admins may open this page
$isAdmin = false;
if (!empty($_SESSION['user_id'])) {
    $q = $pdo->prepare("SELECT is_admin FROM users WHERE id = ?");
    $q->execute([$_SESSION['user_id']]);
    $isAdmin = (bool) $q->fetchColumn();
}
if (!$isAdmin) {
    header("Location: login.php");
    exit;
}

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(16));
}

$categories = ["Fashion", "Electronics", "Accessories", "Beauty", "Footwear", "Lifestyle"];
$tags = ["NEW", "HOT", "SALE", ""];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $result = "error";

    if (hash_equals($_SESSION['csrf'], $_POST['csrf'] ?? '')) {

        $action = $_POST['action'] ?? '';
        $id = (int) ($_POST['id'] ?? 0);

        if ($action === 'add') {
            $name = trim($_POST['name'] ?? '');
            $category = $_POST['category'] ?? '';
            $price = (float) ($_POST['price'] ?? 0);
            $tag = $_POST['tag'] ?? '';
            $file = $_FILES['photo'] ?? null;

            if ($name !== '' and $price > 0 and in_array($category, $categories) and in_array($tag, $tags)
                and $file and $file['error'] === UPLOAD_ERR_OK and $file['size'] <= 3 * 1024 * 1024) {

                $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mime = $finfo->file($file['tmp_name']);

                if (isset($allowed[$mime])) {
                    $slug = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $name), '-'));
                    if ($slug === '') { $slug = 'product'; }
                    $filename = $slug . '-' . bin2hex(random_bytes(3)) . '.' . $allowed[$mime];

                    if (move_uploaded_file($file['tmp_name'], 'assets/images/' . $filename)) {
                        $ins = $pdo->prepare("INSERT INTO products (name, category, price, image, tag) VALUES (?, ?, ?, ?, ?)");
                        $ins->execute([$name, $category, $price, $filename, $tag]);
                        $result = "added";
                    }
                }
            }
        }

        if ($action === 'save' and $id > 0) {
            $name = trim($_POST['name'] ?? '');
            $category = $_POST['category'] ?? '';
            $price = (float) ($_POST['price'] ?? 0);
            $tag = $_POST['tag'] ?? '';

            if ($name !== '' and $price > 0 and in_array($category, $categories) and in_array($tag, $tags)) {
                $up = $pdo->prepare("UPDATE products SET name = ?, category = ?, price = ?, tag = ? WHERE id = ?");
                $up->execute([$name, $category, $price, $tag, $id]);
                $result = "saved";
            }
        }

        if ($action === 'delete' and $id > 0) {
            $del = $pdo->prepare("DELETE FROM products WHERE id = ?");
            $del->execute([$id]);
            $result = "deleted";
        }
    }

    header("Location: admin.php?msg=" . $result);
    exit;
}

$products = $pdo->query("SELECT id, name, category, price, image, tag FROM products ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

$messages = [
    "saved" => "Product saved.",
    "deleted" => "Product deleted.",
    "added" => "Product added.",
    "error" => "Something went wrong. Check the values and try again."
];
$msg = $messages[$_GET['msg'] ?? ''] ?? "";

$pageTitle = "Admin | StoreFussion";
include 'header.php';
?>

<section class="admin-page">

    <h1 class="cart-title">Admin: Products (<?php echo count($products); ?>)</h1>

    <p style="margin-bottom:18px;"><a href="admin-orders.php" class="primary-button">VIEW ORDERS &rarr;</a></p>
    <?php if ($msg !== ""): ?>
        <div class="admin-message"><?php echo htmlspecialchars($msg); ?></div>
    <?php endif; ?>

   <form method="POST" enctype="multipart/form-data" class="admin-add">
        <h3>Add a new product</h3>
        <input type="hidden" name="csrf" value="<?php echo $_SESSION['csrf']; ?>">
        <input type="hidden" name="action" value="add">

        <input type="text" name="name" placeholder="Product name" required>

        <select name="category">
            <?php foreach ($categories as $c): ?>
                <option value="<?php echo $c; ?>"><?php echo $c; ?></option>
            <?php endforeach; ?>
        </select>

        <input type="number" name="price" step="0.01" min="0.01" placeholder="Price ($)" required>

        <select name="tag">
            <?php foreach ($tags as $t): ?>
                <option value="<?php echo $t; ?>"><?php echo $t === '' ? 'No tag' : $t; ?></option>
            <?php endforeach; ?>
        </select>

        <input type="file" name="photo" accept="image/jpeg,image/png,image/webp" required>

        <button type="submit" class="admin-save">ADD PRODUCT</button>
    </form>

    <input type="text" id="adminSearch" class="admin-search" placeholder="Search products by name...">

    <?php foreach ($products as $p): ?>
        <form method="POST" class="admin-row" data-name="<?php echo htmlspecialchars(strtolower($p['name'])); ?>">
            <input type="hidden" name="csrf" value="<?php echo $_SESSION['csrf']; ?>">
            <input type="hidden" name="id" value="<?php echo (int) $p['id']; ?>">

            <img src="assets/images/<?php echo rawurlencode($p['image']); ?>" alt="">

            <input type="text" name="name" value="<?php echo htmlspecialchars($p['name']); ?>" required>

            <select name="category">
                <?php foreach ($categories as $c): ?>
                    <option value="<?php echo $c; ?>" <?php echo $c === $p['category'] ? 'selected' : ''; ?>><?php echo $c; ?></option>
                <?php endforeach; ?>
            </select>

            <input type="number" name="price" step="0.01" min="0.01" value="<?php echo htmlspecialchars($p['price']); ?>" required>

            <select name="tag">
                <?php foreach ($tags as $t): ?>
                    <option value="<?php echo $t; ?>" <?php echo $t === $p['tag'] ? 'selected' : ''; ?>><?php echo $t === '' ? 'No tag' : $t; ?></option>
                <?php endforeach; ?>
            </select>

            <button type="submit" name="action" value="save" class="admin-save">SAVE</button>
            <button type="submit" name="action" value="delete" class="admin-delete"
                data-confirm="1">DELETE</button>
        </form>
    <?php endforeach; ?>

</section>
<script>
document.getElementById("adminSearch").addEventListener("input", function () {
    var term = this.value.toLowerCase().trim();
    document.querySelectorAll(".admin-row").forEach(function (row) {
        row.style.display = row.dataset.name.indexOf(term) !== -1 ? "" : "none";
    });
});
</script>

<script>
(function () {
    var back = document.createElement("div");
    back.className = "fx-modal-back";
    back.innerHTML =
        '<div class="fx-modal">' +
        '<div class="fx-modal-icon">🗑</div>' +
        '<h3>Delete product?</h3>' +
        '<p id="fxDeleteText"></p>' +
        '<div class="fx-modal-actions">' +
        '<button type="button" class="fx-modal-cancel">Keep it</button>' +
        '<button type="button" class="fx-modal-ok danger">Delete</button>' +
        '</div></div>';
    document.body.appendChild(back);

    var pendingForm = null;
    var pendingButton = null;

    function close() { back.classList.remove("show"); }

    document.addEventListener("click", function (e) {
        var btn = e.target.closest(".admin-delete");
        if (!btn) return;
        e.preventDefault();
        pendingForm = btn.form;
        pendingButton = btn;
        var nameInput = pendingForm.querySelector('input[name="name"]');
        var name = nameInput ? nameInput.value : "This product";
        document.getElementById("fxDeleteText").textContent =
            name + " will be removed from your shop. This cannot be undone.";
        back.classList.add("show");
    });

    back.querySelector(".fx-modal-cancel").addEventListener("click", close);
    back.addEventListener("click", function (e) { if (e.target === back) close(); });

    back.querySelector(".fx-modal-ok").addEventListener("click", function () {
        if (!pendingForm) return;
        close();
        pendingForm.requestSubmit(pendingButton);
    });
})();
</script>

<?php include 'footer.php'; ?>