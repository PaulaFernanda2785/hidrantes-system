<div class="auth-dashboard-page">
    <section class="card auth-institutional-card">
        <div class="auth-institutional-brand">
            <img
                class="auth-institutional-logo"
                src="/img/logos/logo.cbmpa.png"
                alt="Logo Corpo de Bombeiros Militar do Estado do Pará"
            >
            <div class="auth-institutional-copy">
                <p class="auth-eyebrow">Identidade institucional</p>
                <strong>Corpo de Bombeiros Militar do Estado do Pará</strong>
                <span>Coordenadoria Estadual de Proteção e Defesa Civil</span>
                <p class="auth-institutional-system">Sistema de Gestão de Hidrantes</p>
            </div>
        </div>
        <span class="auth-institutional-badge">Painel operacional público com acesso interno por perfil</span>
    </section>

    <section class="auth-hero-grid">
        <article class="card auth-hero-card">
            <p class="auth-eyebrow">Sistema de Gestão de Hidrantes</p>
            <h1>Acesso institucional e painel operacional em uma única tela</h1>
            <p class="auth-hero-description">
                Consulte o painel georreferenciado dos hidrantes mesmo antes do login. Para acessar menus, cadastros e operações administrativas, entre com seu usuário conforme o perfil autorizado.
            </p>

            <div class="auth-hero-badges">
                <span class="auth-hero-badge">Painel público de consulta</span>
                <span class="auth-hero-badge is-soft">Login por matrícula funcional</span>
                <span class="auth-hero-badge is-soft">Acesso interno por perfil</span>
            </div>

            <div class="auth-hero-notes">
                <div class="auth-note-item">
                    <strong>Consulta pública</strong>
                    <p>O mapa e o detalhe dos hidrantes podem ser consultados nesta tela sem autenticação.</p>
                </div>
                <div class="auth-note-item">
                    <strong>Acesso ao sistema</strong>
                    <p>Menus, histórico, relatórios e funções de cadastro continuam disponíveis apenas para usuários autenticados.</p>
                </div>
            </div>
        </article>

        <section class="card auth-login-card">
            <div class="auth-login-head">
                <p class="auth-eyebrow">Acesso restrito</p>
                <h2>Entrar no sistema</h2>
                <p>Use sua matrícula funcional e senha para acessar o ambiente interno conforme o seu perfil.</p>
            </div>

            <form method="POST" action="/login" class="form-grid auth-login-form">
                <?= csrf_field() ?>
                <label>
                    Matrícula funcional
                    <input type="text" name="matricula_funcional" required autocomplete="username">
                </label>
                <label>
                    Senha
                    <input type="password" name="senha" required autocomplete="current-password">
                </label>
                <button type="submit">Entrar</button>
            </form>
        </section>
    </section>

    <section class="auth-panel-section">
        <?php require base_path('resources/views/components/operational_panel.php'); ?>
    </section>
</div>
