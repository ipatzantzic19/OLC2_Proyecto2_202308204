// test_constants_expressions.golampi
// Prueba: uso de constantes dentro de expresiones
// Output esperado:
// 200
// 6.28
// 50
// false

const maxItems int32 = 100
const pi float32 = 3.14

func main() {
    // Usar constante en expresión aritmética
    doble := maxItems * 2
    fmt.Println(doble)

    // Constante float en expresión
    circunferencia := pi * 2.0
    fmt.Println(circunferencia)

    // Constante como límite de bucle
    suma := 0
    for i := 0; i < maxItems; i++ {
        suma += i
    }
    // suma = 0+1+...+99 = 4950, pero mostramos maxItems/2
    fmt.Println(maxItems / 2)

    // Constante en condición
    umbral := 200
    fmt.Println(maxItems > umbral)
}