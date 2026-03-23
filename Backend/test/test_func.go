func suma(a int32, b int32) int32 {
    return a + b
}
func dividir(a int32, b int32) (int32, bool) {
    if b == 0 {
        return 0, false
    }
    return a / b, true
}

func factorial(n int32) int32 {
    if n <= 1 {
        return 1
    }
    return n * factorial(n - 1)
}

func incrementar(ptr *int32) {
    // TODO: cuando se implemente escritura por puntero
}
func saludar(nombre string) {
    fmt.Println("Hola,", nombre)
}
func main() {
    // Función simple
    r := suma(3, 4)
    fmt.Println("suma:", r)

    // Múltiples retornos
    resultado, ok := dividir(10, 2)
    if ok {
        fmt.Println("division:", resultado)
    }
    // División por cero
    res2, ok2 := dividir(5, 0)
    fmt.Println("div/0 ok:", ok2, "val:", res2)

    fmt.Println("factorial 5:", factorial(5))

    // Hoisting: saludar está definida DESPUÉS de main
    saludar("Golampi")
}

