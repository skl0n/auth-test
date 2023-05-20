<div class="profile">
    <div class="form">
        <h2 class="text_center">Profile data</h2>

        <?php include 'change_user_field.php'; ?>

        <div class="group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" value="<?= $user->get_username(); ?>" readonly>
        </div>
        <div class="group">
            <label for="email">Email</label>
            <input type="text" id="email" name="email" value="<?= $user->get_email(); ?>" readonly>
        </div>
        <div class="group">
            <label for="description">Description</label>
            <textarea id="description" name="description" readonly><?= $user->get_description(); ?></textarea>
        </div>
        <div class="group">
            <button type="button" class="alert logout" data-user-id="<?= $user->get_id(); ?>">Log out</button>
        </div>
    </div>
</div>