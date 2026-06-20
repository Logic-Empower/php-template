<section class="content">
    <h1>Contact</h1>

    <?php if ($message = flash('success')): ?>
        <p class="alert success"><?= e($message) ?></p>
    <?php endif; ?>

    <?php if ($message = flash('error')): ?>
        <p class="alert error"><?= e($message) ?></p>
    <?php endif; ?>

    <form class="contact-form" action="<?= e(url('/contact.php')) ?>" method="post" novalidate>
        <?= csrf_field() ?>

        <label for="name">Name</label>
        <input id="name" name="name" type="text" value="<?= e(old('name')) ?>" required maxlength="100" autocomplete="name">

        <label for="email">Email</label>
        <input id="email" name="email" type="email" value="<?= e(old('email')) ?>" required maxlength="255" autocomplete="email">

        <label for="phone">Phone</label>
        <input id="phone" name="phone" type="tel" value="<?= e(old('phone')) ?>" maxlength="40" autocomplete="tel">

        <label for="message">Message</label>
        <textarea id="message" name="message" required maxlength="5000" rows="7"><?= e(old('message')) ?></textarea>

        <button type="submit">Send message</button>
    </form>
</section>
