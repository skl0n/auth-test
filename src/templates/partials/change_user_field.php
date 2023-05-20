<?php
    $logged_profiles = user::get_user_logged_profiles(true);
?>
<?php if ($logged_profiles): ?>
<div class="group">
    <label for="change_user">Change user</label>
    <select id="change_user" name="change-user">
        <option value="0" <?= !isset($user) ? 'selected' : ''?>>Login new profile</option>
        <?php foreach ($logged_profiles as $profile): ?>
            <option
                value="<?= $profile->get_id()?>"
                <?= isset($user) && $profile->get_id() == $user->get_id() ? 'selected' : '' ?>
            >
                <?= $profile->get_username(); ?>
            </option>>
        <?php endforeach; ?>
    </select>
</div>
<?php endif; ?>