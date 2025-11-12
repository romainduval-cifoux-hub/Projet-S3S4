<div class="contact-container">
    <h2 class="page-subtitle">Nous contacter</h2>
    <section class="steps">

        
        <h2>Réalisation de votre projet en 4 étapes !</h2>

            <div class="step active">
                <div class="number">1</div>
                <div>
                    <h3>Contact</h3>
                    <p>Discutez de vos envies et besoins. Prendre contact n’engage à rien !</p>
                </div>
            </div>

            <div class="step">
                <div class="number">2</div>
                <div>
                    <h3>Visite</h3>
                    <p>Nous nous déplaçons chez vous pour évaluer le projet et prendre des mesures.</p> 
                </div>
            </div>

            <div class="step">
                <div class="number">3</div>
                <h3>Devis</h3>
            </div>

            <div class="step">
                <div class="number">4</div>
                <h3>Réalisation</h3>
            </div>
        </section>

        <section class="contact-form">
            <h1>Formulaire de contact</h1>
            <p>Remplissez ce formulaire pour entrer en contact avec notre équipe.</p>

            <form method="POST" action="<?= BASE_URL ?>/public/index.php?page=contact_submit">
                <input class="input" type="email" name="email" placeholder="email@exemple.fr" required>
                <input class="input" type="tel" name="phone" placeholder="Numéro de téléphone">
                <textarea class="textarea" name="message" placeholder="Votre message" required></textarea>
                <button class="btn_connexion" type="submit">Contacter</button>
        </form>
    </section>
</div>


