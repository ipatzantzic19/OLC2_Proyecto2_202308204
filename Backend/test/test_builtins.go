// ============================================================
//  ARCHIVO DE PRUEBA - Funciones Embebidas (Built-ins)
//  Golampi Interpreter - OLC2
//  Prueba: fmt.Println, len, now, substr, typeOf
// ============================================================


// ============================================================
//  1. fmt.Println - Impresión de valores
// ============================================================

func testFmtPrintln() {
    fmt.Println("=== TEST: fmt.Println ===")

    // Tipos primitivos simples
    fmt.Println("Hola Mundo")
    fmt.Println(42)
    fmt.Println(3.14)
    fmt.Println(true)
    fmt.Println(false)

    // Múltiples argumentos en una sola llamada
    fmt.Println("Resultado:", 10, true)
    fmt.Println("Texto:", "abc", "def")
    fmt.Println("Mix:", 1, 2.5, false, "fin")

    // Variables de distintos tipos
    var entero int32 = 100
    var decimal float32 = 9.99
    var texto string = "GoLampi"
    var flag bool = true
    fmt.Println(entero)
    fmt.Println(decimal)
    fmt.Println(texto)
    fmt.Println(flag)

    // Rune (debe imprimir el carácter, no el entero)
    var letra rune = 'A'
    var numero rune = '9'
    var especial rune = '\n'
    fmt.Println(letra)
    fmt.Println(numero)

    // Arreglo simple
    var arr [3]int32 = [3]int32{10, 20, 30}
    fmt.Println(arr)

    // Arreglo multidimensional
    var mat [2][2]int32 = [2][2]int32{
        {1, 2},
        {3, 4},
    }
    fmt.Println(mat)

    // Nil
    fmt.Println("Fin fmt.Println")
}


// ============================================================
//  2. len - Longitud de string y arreglo
// ============================================================

func testLen() {
    fmt.Println("=== TEST: len ===")

    // Strings
    var s1 string = "Hola"
    var s2 string = "Compiladores"
    var s3 string = ""
    var s4 string = "abc def"

    fmt.Println(len(s1))       // 4
    fmt.Println(len(s2))       // 12
    fmt.Println(len(s3))       // 0
    fmt.Println(len(s4))       // 7

    // String con Unicode (caracteres multibyte)
    var unicode string = "Golampi"
    fmt.Println(len(unicode))  // 7

    // Arreglos 1D
    var a1 [5]int32
    var a2 [3]string = [3]string{"a", "b", "c"}
    var a3 [1]bool = [1]bool{true}

    fmt.Println(len(a1))       // 5
    fmt.Println(len(a2))       // 3
    fmt.Println(len(a3))       // 1

    // Arreglo 2D - len retorna primera dimension
    var mat [4][2]int32
    fmt.Println(len(mat))      // 4

    // len dentro de un for
    var nombres [3]string = [3]string{"Ana", "Luis", "Maria"}
    var i int32 = 0
    for i < len(nombres) {
        fmt.Println(nombres[i])
        i++
    }

    fmt.Println("Fin len")
}


// ============================================================
//  3. now - Fecha y hora actual
// ============================================================

func testNow() {
    fmt.Println("=== TEST: now ===")

    // Obtener fecha actual
    var fecha string = now()
    fmt.Println(fecha)

    // Usar now() directamente en println
    fmt.Println(now())

    // Guardar y usar len sobre la fecha (formato YYYY-MM-DD HH:MM:SS = 19 chars)
    var fechaActual string = now()
    fmt.Println(len(fechaActual))  // debe ser 19

    fmt.Println("Fin now")
}


// ============================================================
//  4. substr - Extracción de subcadena
// ============================================================

func testSubstr() {
    fmt.Println("=== TEST: substr ===")

    var s string = "Compiladores"

    // Casos normales
    fmt.Println(substr(s, 0, 4))    // Comp
    fmt.Println(substr(s, 4, 4))    // ilad
    fmt.Println(substr(s, 0, 12))   // Compiladores (completo)
    fmt.Println(substr(s, 11, 1))   // s (último caracter)

    // Desde el inicio
    var saludo string = "Hola Mundo"
    fmt.Println(substr(saludo, 0, 4))   // Hola
    fmt.Println(substr(saludo, 5, 5))   // Mundo
    fmt.Println(substr(saludo, 0, 10))  // Hola Mundo

    // Subcadena de longitud 1
    fmt.Println(substr("ABCDE", 0, 1))  // A
    fmt.Println(substr("ABCDE", 4, 1))  // E

    // Casos límite - índice inválido retorna nil
    var resultado string = substr(s, 0, 100)  // fuera de rango → nil
    fmt.Println(resultado)

    // Combinar con len
    var palabra string = "GoLampi"
    var longitud int32 = len(palabra)
    fmt.Println(substr(palabra, 0, longitud))  // GoLampi completo

    fmt.Println("Fin substr")
}


// ============================================================
//  5. typeOf - Tipo de una variable
// ============================================================

func testTypeOf() {
    fmt.Println("=== TEST: typeOf ===")

    // Tipos primitivos declarados explícitamente
    var entero int32 = 42
    var decimal float32 = 3.14
    var booleano bool = true
    var cadena string = "texto"
    var caracter rune = 'Z'

    fmt.Println(typeOf(entero))     // int32
    fmt.Println(typeOf(decimal))    // float32
    fmt.Println(typeOf(booleano))   // bool
    fmt.Println(typeOf(cadena))     // string
    fmt.Println(typeOf(caracter))   // rune

    // Tipos inferidos con :=
    x := 10
    y := 2.5
    z := false
    w := "dinamico"

    fmt.Println(typeOf(x))    // int32
    fmt.Println(typeOf(y))    // float32
    fmt.Println(typeOf(z))    // bool
    fmt.Println(typeOf(w))    // string

    // Arreglo
    var arr [3]int32 = [3]int32{1, 2, 3}
    fmt.Println(typeOf(arr))  // []int32

    // Literales directos
    fmt.Println(typeOf(100))       // int32
    fmt.Println(typeOf(1.5))       // float32
    fmt.Println(typeOf(true))      // bool
    fmt.Println(typeOf("hola"))    // string

    fmt.Println("Fin typeOf")
}


// ============================================================
//  6. PRUEBAS COMBINADAS
// ============================================================

func testCombinados() {
    fmt.Println("=== TEST: Combinados ===")

    // substr + len
    var frase string = "Hola Mundo"
    var mitad int32 = len(frase) / 2
    fmt.Println(substr(frase, 0, mitad))       // Hola M (5 chars)
    fmt.Println(substr(frase, mitad, mitad))   // undo  (5 chars)

    // typeOf + len combinados
    var lista [4]string = [4]string{"uno", "dos", "tres", "cuatro"}
    fmt.Println(typeOf(lista))    // []string
    fmt.Println(len(lista))       // 4

    // len de resultado de substr
    var sub string = substr("Compiladores", 0, 5)
    fmt.Println(sub)              // Compi
    fmt.Println(len(sub))         // 5

    // now + substr (extraer solo la fecha sin la hora)
    var fechaHora string = now()
    var soloFecha string = substr(fechaHora, 0, 10)  // YYYY-MM-DD
    fmt.Println(soloFecha)

    // Iterar con len y acceso a arreglo
    var nums [5]int32 = [5]int32{10, 20, 30, 40, 50}
    var j int32 = 0
    for j < len(nums) {
        fmt.Println(typeOf(nums[j]), "=", nums[j])
        j++
    }

    fmt.Println("Fin Combinados")
}


// ============================================================
//  FUNCIÓN PRINCIPAL
// ============================================================

func main() {
    fmt.Println("============================================")
    fmt.Println("  PRUEBA DE FUNCIONES EMBEBIDAS - GOLAMPI  ")
    fmt.Println("============================================")

    testFmtPrintln()
    fmt.Println("--------------------------------------------")

    testLen()
    fmt.Println("--------------------------------------------")

    testNow()
    fmt.Println("--------------------------------------------")

    testSubstr()
    fmt.Println("--------------------------------------------")

    testTypeOf()
    fmt.Println("--------------------------------------------")

    testCombinados()
    fmt.Println("--------------------------------------------")

    fmt.Println("============================================")
    fmt.Println("         TODAS LAS PRUEBAS FINALIZADAS      ")
    fmt.Println("============================================")
}