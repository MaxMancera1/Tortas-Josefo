# POST create de la API de productos. La foto va como archivo.
# Uso: ruby api/post_producto.rb
require "net/http"
require "json"

ruta = File.expand_path("../image/uploads/torta-jamon.png", __dir__)
abort("No encuentro la imagen: #{ruta}") unless File.file?(ruta)

uri = URI("http://127.0.0.1:8081/api/productos.php")
req = Net::HTTP::Post.new(uri)
archivo = File.open(ruta, "rb")

req.set_form([
  ["nombre", "Torta de jamón Ruby"],
  ["categoria", "Tortas"],
  ["precio", "48"],
  ["foto", archivo],
], "multipart/form-data")

res = Net::HTTP.start(uri.hostname, uri.port) { |http| http.request(req) }
archivo.close

puts res.code
puts JSON.pretty_generate(JSON.parse(res.body))
