<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/user_functions.php';


// Check if the user is logged in
if (!isLoggedIn()) {
    $_SESSION['flash_message'] = "Debes iniciar sesión para acceder a tu perfil.";
    $_SESSION['flash_type'] = 'warning';
    header('Location: ' . url('login.php'));
    exit;
}

$userId = $_SESSION['user_id'];
$user = getUserById($userId);
$userProfile = getUserProfile($userId);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $bio = filter_input(INPUT_POST, 'bio', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $location = filter_input(INPUT_POST, 'location', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $website = filter_input(INPUT_POST, 'website', FILTER_SANITIZE_URL);

    // Update user information
    $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
    $stmt->execute([$name, $email, $userId]);

    // Update or insert user profile
    if ($userProfile) {
        $stmt = $pdo->prepare("UPDATE user_profiles SET bio = ?, location = ?, website = ? WHERE user_id = ?");
        $stmt->execute([$bio, $location, $website, $userId]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO user_profiles (user_id, bio, location, website) VALUES (?, ?, ?, ?)");
        $stmt->execute([$userId, $bio, $location, $website]);
    }

    // Set success message
    $_SESSION['flash_message'] = 'Perfil actualizado exitosamente.';
    $_SESSION['flash_type'] = 'success';

    header('Location: ' . url('profile.php'));
    exit;
}

$pageTitle = "Perfil de Usuario";
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <h1 class="mb-4">Perfil de Usuario</h1>

    <form method="POST">
        <div class="mb-3">
            <label for="name" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="bio" class="form-label">Biografía</label>
            <textarea class="form-control" id="bio" name="bio" rows="3"><?php echo htmlspecialchars($userProfile['bio'] ?? ''); ?></textarea>
        </div>
        <div class="mb-3">
            <label for="location" class="form-label">Ubicación</label>
            <input type="text" class="form-control" id="location" name="location" value="<?php echo htmlspecialchars($userProfile['location'] ?? ''); ?>">
        </div>
        <div class="mb-3">
            <label for="website" class="form-label">Sitio Web</label>
            <input type="url" class="form-control" id="website" name="website" value="<?php echo htmlspecialchars($userProfile['website'] ?? ''); ?>">
        </div>
        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>