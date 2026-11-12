#!/bin/sh
# Equivalente a Postman desde la terminal (no uses el navegador para este punto).
BASE=http://127.0.0.1:8081/api

pedir() {
  echo
  echo "======== $1 ========"
  shift
  curl -sS -w "\nHTTP %{http_code}\n" "$@"
  echo
}

id_de() {
  python3 -c "import sys,json; print(json.load(sys.stdin).get('id',''))"
}

echo "### PRODUCTOS"
pedir "GET all" "$BASE/productos.php"
pedir "GET one" "$BASE/productos.php?id=1"
CREA=$(curl -sS -X POST "$BASE/productos.php" -H "Content-Type: application/json" \
  -d '{"nombre":"Torta terminal","categoria":"Tortas","precio":48,"foto":"torta-cubana.png"}')
echo
echo "======== POST create ========"
echo "$CREA"
echo
PID=$(printf '%s' "$CREA" | id_de)
pedir "POST update" -X POST "$BASE/productos.php?id=$PID" \
  -H "Content-Type: application/json" \
  -d '{"nombre":"Torta terminal editada","precio":49}'
pedir "DELETE" -X DELETE "$BASE/productos.php?id=$PID"

echo "### PROMOCIONES"
pedir "GET all" "$BASE/promociones.php"
pedir "GET one" "$BASE/promociones.php?id=1"
CREA=$(curl -sS -X POST "$BASE/promociones.php" -H "Content-Type: application/json" \
  -d '{"titulo":"Promo terminal","descripcion":"Creada por curl","precio":70,"vigencia":"2026-12-31","imagen":"promo-combo.png"}')
echo
echo "======== POST create ========"
echo "$CREA"
echo
PID=$(printf '%s' "$CREA" | id_de)
pedir "POST update" -X POST "$BASE/promociones.php?id=$PID" \
  -H "Content-Type: application/json" \
  -d '{"titulo":"Promo terminal editada"}'
pedir "DELETE" -X DELETE "$BASE/promociones.php?id=$PID"

echo "### SUCURSALES"
pedir "GET all" "$BASE/sucursales.php"
pedir "GET one" "$BASE/sucursales.php?id=2"
CREA=$(curl -sS -X POST "$BASE/sucursales.php" -H "Content-Type: application/json" \
  -d '{"nombre":"Sucursal terminal","direccion":"Prueba curl Cortazar","telefono":"411 000 0000"}')
echo
echo "======== POST create ========"
echo "$CREA"
echo
PID=$(printf '%s' "$CREA" | id_de)
pedir "POST update" -X POST "$BASE/sucursales.php?id=$PID" \
  -H "Content-Type: application/json" \
  -d '{"nombre":"Sucursal terminal editada"}'
pedir "DELETE" -X DELETE "$BASE/sucursales.php?id=$PID"
