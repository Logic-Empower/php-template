    </main>
    <footer class="site-footer">
        <small>&copy; <?= date('Y') ?> <?= e((string) env_value('APP_NAME', 'PHP Template')) ?></small>
    </footer>
    <script src="<?= e(url('/assets/js/main.js')) ?>" defer></script>
</body>
</html>
