# Sube la foto como archivo (multipart), no como nombre en texto.
# La guarda la API de productos. Sucursales no tiene campo de imagen.
# Uso: ruby api/post_sucursal.rb
#      ruby api/post_sucursal.rb image/torta-cubana.png
require "net/http"

ruta = ARGV[0] ? File.expand_path(ARGV[0]) : File.expand_path("../image/uploads/torta-cubana.png", __dir__)
abort("No encuentro la imagen: #{ruta}") unless File.file?(ruta)

uri = URI("http://127.0.0.1:8081/api/productos.php")
req = Net::HTTP::Post.new(uri)
archivo = File.open(ruta, "rb")

req.set_form([
  ["nombre", "Torta desde Ruby"],
  ["categoria", "Tortas"],
  ["precio", "52"],
  ["foto", archivo],
], "multipart/form-data")

res = Net::HTTP.start(uri.hostname, uri.port) { |http| http.request(req) }
archivo.close

puts res.code
puts res.body
