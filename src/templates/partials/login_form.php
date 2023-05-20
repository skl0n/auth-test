<div class="login-form">
    <form class="form">
        <?php include 'change_user_field.php'; ?>
        <div>
            <h2 class="text_center">Login</h2>
        </div>
        <?php if (!empty($error)): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>
        <div class="group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" placeholder="Input username" value="<?= $username ?? '';?>">
        </div>
        <div class="group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Input password" value="<?= $password ?? '';?>">
        </div>
        <div class="group">
            <button type="submit" class="alert">Log in</button>
        </div>
    </form>

    <div class="authorization_congratulations">
        <div class="congratulations_block">
            <h3>You have successfully logged in</h3>
        </div>
    </div>
</div>