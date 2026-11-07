
<?php
$pageTitle = 'Inicio';
require_once __DIR__ . '/includes/header.php';
?>

<main>
    <!-- SECCIÓN PRINCIPAL -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center gy-5">

                <div class="col-lg-6 hero-copy">
                    <span class="eyebrow">TU ESPACIO DE BELLEZA</span>

                    <h1>
                        Resalta tu belleza,<br>
                        <span>define tu estilo.</span>
                    </h1>

                    <p class="hero-description">
                        Un espacio creado para consentirte. Disfruta de nuestros
                        servicios de belleza y deja tu imagen en manos de profesionales.
                    </p>

                    <div class="d-flex flex-wrap gap-3 mt-4">
                        <a href="servicios.php" class="btn btn-primary btn-main">
                            Conoce nuestros servicios
                        </a>

                        <a href="nosotros.php" class="btn btn-outline-light btn-secondary-hero">
                            Conócenos
                        </a>
                    </div>

                    <div class="hero-note">
                        <span class="note-line"></span>
                        Cuidado, estilo y confianza en un solo lugar
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="hero-image-wrap">

                        <img
                            src="https://images.unsplash.com/photo-1560066984-138dadb4c035?auto=format&fit=crop&w=1100&q=85"
                            alt="Interior elegante de un salón de belleza"
                            class="hero-image"
                        >

                        <div class="hero-image-label">
                            <span>ESTILO &amp; BELLEZA</span>
                            <small>Un momento para ti</small>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- SECCIÓN DE BIENVENIDA -->
    <section class="welcome-section">
        <div class="container text-center">

            <span class="eyebrow eyebrow-dark">BIENVENIDA</span>

            <h2>Tu belleza merece lo mejor</h2>

            <p class="section-intro">
                Queremos que cada visita sea una experiencia especial.
                Nuestro objetivo es ayudarte a sentirte bien, renovar tu estilo
                y disfrutar de un momento para ti.
            </p>

            <div class="row g-4 mt-3">

                <div class="col-md-4">
                    <div class="feature-card">
                        <span class="feature-number">01</span>

                        <h3>Atención personalizada</h3>

                        <p>
                            Escuchamos tus ideas para encontrar el estilo
                            que mejor va contigo.
                        </p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="feature-card">
                        <span class="feature-number">02</span>

                        <h3>Calidad y cuidado</h3>

                        <p>
                            Trabajamos para ofrecerte una atención cuidadosa
                            en cada servicio.
                        </p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="feature-card">
                        <span class="feature-number">03</span>

                        <h3>Un espacio para ti</h3>

                        <p>
                            Disfruta de un ambiente agradable pensado
                            para tu bienestar.
                        </p>
                    </div>
                </div>

            </div>

            <a href="servicios.php" class="btn btn-outline-primary btn-more mt-4">
                Explorar servicios
            </a>

        </div>
    </section>
</main>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
