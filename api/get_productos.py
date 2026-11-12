import json
import urllib.request

URL = "http://127.0.0.1:8081/api/productos.php"
datos = json.load(urllib.request.urlopen(URL))
print(json.dumps(datos, indent=2, ensure_ascii=False))
