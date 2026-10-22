<section class="page-heading">
    <p>PRODUCT CATALOG</p>
    <h1>Solar products</h1>
    <p>Explore the main components used in solar energy systems. Ask our team for a quote based on your requirements.</p>
</section>
<div class="card-grid products-grid">
    <?php $res=$conn->query('SELECT * FROM products WHERE active=1 ORDER BY category,name');while($p=$res->fetch_assoc()): ?>
    <article class="product-card">
        <div class="product-symbol">
            <?php echo ['Panels'=>'▦','Inverters'=>'ϟ','Batteries'=>'▰','Mounting'=>'⌂','Wires'=>'⌁'][$p['category']]??'☀'; ?>
        </div>
        <span class="tag">
            <?php echo h($p['category']); ?>
        </span>
        <h3>
            <?php echo h($p['name']); ?>
        </h3>
        <p>
            <?php echo h($p['description']); ?>
        </p>
        <p class="price">
            <?php echo (float)$p['price']>0?'₱'.number_format((float)$p['price'],2):'Request a quote'; ?>
        </p>
        <a class="text-link" href="index.php?page=contact">Ask about this product →</a>
    </article>
    <?php endwhile; ?>
</div>
<p class="muted">Prices and inventory shown in this catalog are managed by the business and should be confirmed before purchase.</p>
