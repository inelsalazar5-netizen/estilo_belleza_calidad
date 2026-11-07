
<?php
require_once __DIR__ . '/includes/header.php';
?>

<main class="contact-page">

    <!-- Encabezado -->
    <section class="contact-hero">
        <div class="container text-center">
            <span>ESTAMOS PARA TI</span>
            <h1>Contáctanos</h1>
            <p>Nos encantará atenderte y ayudarte a resaltar tu belleza.</p>
        </div>
    </section>

    <!-- Contenido principal -->
    <section class="container py-5">
        <div class="row g-5 align-items-start">

            <!-- Formulario -->
            <div class="col-lg-6">
                <div class="contact-card">

                    <span class="contact-label">ESCRÍBENOS</span>
                    <h2>Envíanos un mensaje</h2>

                    <p>
                        ¿Tienes alguna pregunta o deseas conocer nuestros
                        servicios? Completa el siguiente formulario.
                    </p>

                    <form action="#" method="POST">

                        <div class="mb-3">
                            <label for="nombre" class="form-label">
                                Nombre completo
                            </label>
                            <input
                                type="text"
                                class="form-control"
                                id="nombre"
                                name="nombre"
                                placeholder="Escribe tu nombre"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">
                                Correo electrónico
                            </label>
                            <input
                                type="email"
                                class="form-control"
                                id="email"
                                name="email"
                                placeholder="tu@email.com"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label for="telefono" class="form-label">
                                Teléfono
                            </label>
                            <input
                                type="tel"
                                class="form-control"
                                id="telefono"
                                name="telefono"
                                placeholder="Tu número de teléfono"
                            >
                        </div>

                        <div class="mb-3">
                            <label for="mensaje" class="form-label">
                                Mensaje
                            </label>
                            <textarea
                                class="form-control"
                                id="mensaje"
                                name="mensaje"
                                rows="5"
                                placeholder="¿En qué podemos ayudarte?"
                                required
                            ></textarea>
                        </div>

                        <button type="submit" class="contact-button">
                            Enviar mensaje
                        </button>

                    </form>
                </div>
            </div>

            <!-- Información e imagen -->
            <div class="col-lg-6">

                <div class="contact-image-container">
                    <img
                        src="assets/img/ambiente.jpg"
                        alt="Ambiente de Estilo & Belleza"
                        class="contact-image"
                    >
                </div>

                <div class="contact-information mt-4">

                    <h3>Información de contacto</h3>

                    <div class="contact-item">
                        <span class="contact-icon">☎</span>
                        <div>
                            <strong>Teléfono</strong>
                            <p>Contáctanos para más información.</p>
                        </div>
                    </div>

                    <div class="contact-item">
                        <span class="contact-icon">✉</span>
                        <div>
                            <strong>Correo electrónico</strong>
                            <p>Estamos disponibles para atender tus consultas.</p>
                        </div>
                    </div>

                    <div class="contact-item">
                        <span class="contact-icon">⌖</span>
                        <div>
                            <strong>Ubicación</strong>
                            <p>Visítanos y disfruta de nuestros servicios.</p>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </section>

    <!-- Mensaje final -->
    <section class="contact-bottom text-center">
        <div class="container">
            <h2>¡Será un placer atenderte!</h2>
            <p>En Estilo & Belleza, cada detalle está pensado para ti.</p>
        </div>
    </section>

</main>

<style>
.contact-page {
    color: #75636a;
}

.contact-hero {
    background: linear-gradient(135deg, #4b303a, #80515f, #a66c7b);
    padding: 65px 20px;
    color: white;
}

.contact-hero span {
    color: #e8c9a0;
    font-size: 13px;
    letter-spacing: 4px;
    font-weight: bold;
}

.contact-hero h1 {
    font-family: 'Playfair Display', serif;
    font-size: 48px;
    margin: 12px 0;
}

.contact-hero p {
    color: #f8e9ed;
    margin-bottom: 0;
}

.contact-card {
    background: white;
    padding: 35px;
    border-radius: 18px;
    box-shadow: 0 8px 30px rgba(74, 48, 56, 0.08);
}

.contact-label {
    color: #b76e79;
    font-size: 13px;
    letter-spacing: 3px;
    font-weight: bold;
}

.contact-card h2 {
    font-family: 'Playfair Display', serif;
    color: #4a3038;
    font-size: 32px;
    margin: 12px 0;
}

.contact-card > p {
    line-height: 1.7;
    margin-bottom: 25px;
}

.contact-card .form-label {
    color: #4a3038;
    font-weight: 600;
}

.contact-card .form-control {
    border: 1px solid #eadde0;
    border-radius: 10px;
    padding: 12px 14px;
}

.contact-card .form-control:focus {
    border-color: #b76e79;
    box-shadow: 0 0 0 0.2rem rgba(183, 110, 121, 0.15);
}

.contact-button {
    width: 100%;
    border: none;
    background: #b76e79;
    color: white;
    padding: 14px;
    border-radius: 30px;
    font-weight: 600;
    transition: 0.3s;
}

.contact-button:hover {
    background: #4a3038;
}

.contact-image-container {
    overflow: hidden;
    border-radius: 18px;
    box-shadow: 0 10px 25px rgba(74, 48, 56, 0.12);
}

.contact-image {
    width: 100%;
    height: 300px;
    object-fit: cover;
    display: block;
}

.contact-information {
    background: #fff8f9;
    padding: 28px;
    border-radius: 18px;
}

.contact-information h3 {
    font-family: 'Playfair Display', serif;
    color: #4a3038;
    margin-bottom: 25px;
}

.contact-item {
    display: flex;
    align-items: flex-start;
    gap: 15px;
    margin-bottom: 20px;
}

.contact-item:last-child {
    margin-bottom: 0;
}

.contact-icon {
    background: white;
    color: #b76e79;
    width: 42px;
    height: 42px;
    min-width: 42px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 21px;
}

.contact-item strong {
    color: #4a3038;
}

.contact-item p {
    margin: 5px 0 0;
}

.contact-bottom {
    background: #fff8f9;
    padding: 45px 20px;
}

.contact-bottom h2 {
    font-family: 'Playfair Display', serif;
    color: #4a3038;
}

.contact-bottom p {
    margin-bottom: 0;
}

@media (max-width: 768px) {
    .contact-hero h1 {
        font-size: 36px;
    }

    .contact-card {
        padding: 25px;
    }
}
</style>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
