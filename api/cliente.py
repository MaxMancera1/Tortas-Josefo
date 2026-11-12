# Sprint 09 — cliente Python (una API: productos)
# GET all, GET one, POST create, POST update, DELETE
import json
import urllib.request

BASE = "http://127.0.0.1:8081/api/productos.php"


def pedir(titulo, url, metodo="GET", cuerpo=None):
    print("\n========", titulo, "========")
    data = None
    headers = {}
    if cuerpo is not None:
        data = json.dumps(cuerpo).encode()
        headers["Content-Type"] = "application/json"
    req = urllib.request.Request(url, data=data, headers=headers, method=metodo)
    with urllib.request.urlopen(req) as r:
        texto = r.read().decode()
        print("HTTP", r.status)
        print(texto)
        return json.loads(texto)


pedir("GET all", BASE)
pedir("GET one", BASE + "?id=1")

creado = pedir(
    "POST create",
    BASE,
    "POST",
    {"nombre": "Torta Python", "categoria": "Tortas", "precio": 47, "foto": "torta-cubana.png"},
)
pid = creado["id"]

pedir(
    "POST update",
    f"{BASE}?id={pid}",
    "POST",
    {"nombre": "Torta Python editada", "precio": 48},
)
pedir("DELETE", f"{BASE}?id={pid}", "DELETE")
