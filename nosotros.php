
<?php
require_once __DIR__ . '/includes/header.php';
?>

<main class="about-page">

    <!-- Encabezado -->
    <section class="about-hero">
        <div class="container text-center">
            <span class="about-subtitle">CONÓCENOS</span>
            <h1>Sobre Nosotros</h1>
            <p>Un espacio creado para resaltar tu belleza y cuidar de ti.</p>
        </div>
    </section>

    <!-- Información del salón -->
    <section class="container py-5">
        <div class="row align-items-center g-5">

            <div class="col-lg-6">
                <div class="about-image-container">
                    <img
                        src="assets/img/ambiente.jpg"
                        alt="Ambiente de Estilo & Belleza"
                        class="about-image"
                    >
                </div>
            </div>

            <div class="col-lg-6">
                <span class="about-label">ESTILO & BELLEZA</span>

                <h2 class="about-title">
                    Tu belleza es nuestra inspiración
                </h2>

                <p>
                    En Estilo & Belleza combinamos la pasión por el estilo
                    con la atención al detalle para ofrecerte una experiencia
                    agradable y especial.
                </p>

                <p>
                    Nuestro objetivo es brindarte servicios de peluquería
                    y belleza con dedicación, profesionalismo y atención
                    personalizada.
                </p>

                <p>
                    Queremos que cada visita sea un momento para consentirte,
                    renovar tu imagen y sentirte aún más hermosa.
                </p>

                <a href="servicios.php" class="about-button">
                    Conoce nuestros servicios
                </a>
            </div>

        </div>
    </section>

    <!-- Misión y visión -->
    <section class="about-values">
        <div class="container py-5">
            <div class="row g-4">

                <div class="col-md-6">
                    <div class="value-card">
                        <div class="value-icon">♡</div>
                        <h3>Nuestra misión</h3>
                        <p>
                            Ofrecer servicios de belleza y cuidado personal
                            con calidad, dedicación y atención personalizada,
                            haciendo que cada cliente se sienta especial.
                        </p>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="value-card">
                        <div class="value-icon">✧</div>
                        <h3>Nuestra visión</h3>
                        <p>
                            Ser un salón de belleza reconocido por su
                            atención, creatividad y compromiso con la
                            satisfacción de nuestros clientes.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </section>

</main>

<style>
.about-page {
    color: #75636a;
}

.about-hero {
    background: linear-gradient(135deg, #4b303a, #80515f, #a66c7b);
    padding: 65px 20px;
    color: white;
}

.about-subtitle {
    color: #e8c9a0;
    letter-spacing: 4px;
    font-size: 13px;
    font-weight: 600;
}

.about-hero h1 {
    font-family: 'Playfair Display', serif;
    font-size: 48px;
    margin: 12px 0;
}

.about-hero p {
    margin-bottom: 0;
    color: #f8e9ed;
}

.about-image-container {
    overflow: hidden;
    border-radius: 18px;
    box-shadow: 0 12px 30px rgba(74, 48, 56, 0.15);
}

.about-image {
    width: 100%;
    height: 440px;
    object-fit: cover;
    display: block;
}

.about-label {
    color: #b76e79;
    letter-spacing: 3px;
    font-size: 13px;
    font-weight: bold;
}

.about-title {
    font-family: 'Playfair Display', serif;
    color: #4a3038;
    font-size: 36px;
    margin: 15px 0 20px;
}

.about-page p {
    line-height: 1.8;
}

.about-button {
    display: inline-block;
    background: #b76e79;
    color: white;
    text-decoration: none;
    padding: 13px 25px;
    border-radius: 30px;
    margin-top: 15px;
    transition: 0.3s;
}

.about-button:hover {
    background: #4a3038;
    color: white;
}

.about-values {
    background: #fff8f9;
}

.value-card {
    background: white;
    padding: 35px;
    height: 100%;
    border-radius: 16px;
    text-align: center;
    box-shadow: 0 5px 20px rgba(74, 48, 56, 0.06);
}

.value-icon {
    font-size: 38px;
    color: #b76e79;
    margin-bottom: 12px;
}

.value-card h3 {
    font-family: 'Playfair Display', serif;
    color: #4a3038;
    margin-bottom: 15px;
}

.value-card p {
    margin-bottom: 0;
}

@media (max-width: 768px) {
    .about-hero h1 {
        font-size: 36px;
    }

    .about-title {
        font-size: 29px;
    }

    .about-image {
        height: 300px;
    }
}
</style>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
