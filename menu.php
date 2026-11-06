<?php
// El menú público sale de la tabla productos: lo que se edita en el panel es lo que ve la gente
include_once __DIR__ . '/admin/models/producto/producto.class.php';
$app = new Producto();
$menu = [];
foreach ($app->getAll() as $producto) {
    $menu[$producto['categoria']][] = $producto;
}
$notas = [
    'Tortas' => 'Pan bolillo, aguacate, jitomate y salsa. Con queso asadero, $10 más.',
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Menú | Tortas Josefo</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="css/style.css">
  <link rel="icon" href="image/favicon.svg" type="image/svg+xml">
  <link rel="icon" href="image/favicon.png" type="image/png">
</head>
<body class="bg-orange-50 font-sans">
  <div class="max-w-6xl mx-auto px-4 py-6 space-y-5">
    <section class="relative overflow-hidden rounded-xl shadow-md min-h-[220px] md:min-h-[280px]">
      <img
        src="image/Gemini_Generated_Image_8zbhyz8zbhyz8zbh.png"
        alt="Puesto de Tortas Josefo"
        class="absolute inset-0 h-full w-full object-cover"
      >
      <div class="absolute inset-0 bg-black/35"></div>
      <div class="relative grid gap-4 p-4 md:grid-cols-3 md:items-center md:p-6">
        <img
          src="image/Gemini_Generated_Image_fb7ji7fb7ji7fb7j.png"
          alt="Torta cubana"
          class="h-36 w-full rounded-lg object-cover md:h-48"
        >
        <div class="text-center text-white drop-shadow-md">
          <p class="text-sm font-semibold tracking-wide">¡Las auténticas!</p>
          <h1 class="titulo-banner text-3xl font-black uppercase leading-none text-lime-400 md:text-5xl">Tortas Josefo</h1>
          <p class="mt-2 text-sm">Sabor que te conquistará</p>
          <p class="mt-3 inline-block rounded bg-red-600 px-3 py-1 text-xs font-bold uppercase">Especialidad: la cubana</p>
        </div>
        <img
          src="image/banner-lado.png"
          alt="Menú del puesto"
          class="hidden h-36 w-full rounded-lg object-cover md:block md:h-48"
        >
      </div>
    </section>
    <nav class="rounded-xl bg-orange-500 text-white shadow-md">
      <div class="flex flex-wrap items-center justify-between gap-3 px-4 py-3">
        <a href="index.html" class="text-lg font-bold uppercase tracking-wide">Tortas Josefo</a>
        <div class="flex flex-wrap items-center gap-4 text-sm">
          <a href="index.html" class="font-bold">Home</a>
          <a href="menu.php" class="hover:underline">Menú</a>
          <a href="ordenar.html" class="hover:underline">Ordenar</a>
          <a href="sucursales.html" class="hover:underline">Sucursales</a>
          <a href="nosotros.html" class="hover:underline">Nosotros</a>
          <a href="mision.html" class="hover:underline">Misión</a>
          <a href="servicios.html" class="hover:underline">Servicios</a>
          <a href="historia.html" class="hover:underline">Historia</a>
          <a href="promociones.html" class="hover:underline">Promociones</a>
          <a href="preguntas.html" class="hover:underline">Preguntas</a>
          <a href="galeria.html" class="hover:underline">Galería</a>
        </div>
      </div>
    </nav>

    <section class="py-8 text-center">
      <h1 class="text-4xl font-semibold">Menú</h1>
      <p class="mt-3 text-xl text-gray-800">Tortas, hamburguesas, quesadillas, papas y aguas.</p>
    </section>

    <?php foreach ($app->categorias as $categoria): if (empty($menu[$categoria])) continue; ?>
    <h2 class="text-2xl font-semibold"><?php echo $categoria; ?></h2>
    <?php if (isset($notas[$categoria])): ?>
    <p class="text-gray-700"><?php echo $notas[$categoria]; ?></p>
    <?php endif; ?>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 sm:gap-6">
      <?php foreach ($menu[$categoria] as $producto): ?>
      <figure class="tarjeta text-center">
        <img class="aspect-square w-full rounded-lg object-cover" src="image/uploads/<?php echo htmlspecialchars($producto['foto']); ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
        <figcaption class="mt-2 font-semibold"><?php echo htmlspecialchars($producto['nombre']); ?></figcaption>
        <p class="mt-1 precio">$<?php echo number_format($producto['precio'], fmod($producto['precio'], 1) ? 2 : 0); ?></p>
      </figure>
      <?php endforeach; ?>
    </div>
    <?php endforeach; ?>

    <p class="text-center text-gray-800">Pide en el puesto o en <a href="ordenar.html" class="text-orange-600 underline">Ordenar</a>.</p>
    <div class="tarjeta mx-auto flex max-w-md flex-col items-center gap-2 text-center">
      <p class="font-semibold text-orange-900">Comparte el menú</p>
      <div id="qr-menu"></div>
      <p class="text-sm text-gray-600">Escanea para abrir esta página en el celular.</p>
    </div>
    <footer class="rounded-xl bg-orange-600 px-4 py-4 text-center text-sm text-white shadow-md">
      <p>Tortas Josefo · Cortazar, Gto.</p>
      <p>Antonio Madrazo y Colegio Militar · 411 103 8297</p>
      <p class="mt-1"><a href="admin/login.php" class="text-xs underline">Acceso dueño</a></p>
    </footer>
  </div>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
  <script>
    new QRCode(document.getElementById('qr-menu'), {
      text: window.location.href,
      width: 128,
      height: 128
    });
  </script>
</body>
</html>
