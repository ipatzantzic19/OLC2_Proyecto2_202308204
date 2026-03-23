// ============================================================
//  PRUEBAS DE PUNTEROS – FASE 5 GOLAMPI
//  Archivo: test_pointers.golampi
// ============================================================

func main() {
    testPointerBasic()
    testPointerAssignment()
    testPointerAsParam()
    testPointerMultipleVars()
    testPointerWithArrays()
}

// ── 1. Puntero básico: & y * ──────────────────────────────────
func testPointerBasic() {
    fmt.Println("=== testPointerBasic ===")

    var x int32 = 10
    var p *int32 = &x

    // Leer via puntero (desreferencia)
    fmt.Println(*p)   // 10

    // Modificar via puntero
    *p = 20
    fmt.Println(x)    // 20  (x cambió porque p apunta a x)
    fmt.Println(*p)   // 20
}

// ── 2. Asignaciones compuestas via puntero ─────────────────────
func testPointerAssignment() {
    fmt.Println("=== testPointerAssignment ===")

    var n int32 = 100
    var p *int32 = &n

    *p += 50
    fmt.Println(n)   // 150

    *p -= 30
    fmt.Println(n)   // 120

    *p *= 2
    fmt.Println(n)   // 240

    *p /= 4
    fmt.Println(n)   // 60
}

// ── 3. Puntero como parámetro de función ───────────────────────
func doubleValue(p *int32) {
    *p = *p * 2
}

func increment(p *int32) {
    *p += 1
}

func swap(a *int32, b *int32) {
    var temp int32 = *a
    *a = *b
    *b = temp
}

func testPointerAsParam() {
    fmt.Println("=== testPointerAsParam ===")

    var x int32 = 5
    doubleValue(&x)
    fmt.Println(x)   // 10

    increment(&x)
    fmt.Println(x)   // 11

    var a int32 = 3
    var b int32 = 7
    swap(&a, &b)
    fmt.Println(a)   // 7
    fmt.Println(b)   // 3
}

// ── 4. Varios punteros a la misma variable ─────────────────────
func testPointerMultipleVars() {
    fmt.Println("=== testPointerMultipleVars ===")

    var x int32 = 42
    var p1 *int32 = &x
    var p2 *int32 = &x

    *p1 = 100
    fmt.Println(*p2)  // 100 (ambos apuntan a x)
    fmt.Println(x)    // 100
}

// ── 5. Puntero con arreglos (ya probado, confirmar) ────────────
func fillArray(a *[5]int32) {
    for i := 0; i < 5; i++ {
        a[i] = i * 10
    }
}

func sumByRef(a *[3]int32) int32 {
    var total int32 = 0
    for i := 0; i < 3; i++ {
        total += a[i]
    }
    return total
}

func testPointerWithArrays() {
    fmt.Println("=== testPointerWithArrays ===")

    var arr [5]int32
    fillArray(&arr)
    fmt.Println(arr[0])  // 0
    fmt.Println(arr[1])  // 10
    fmt.Println(arr[2])  // 20
    fmt.Println(arr[3])  // 30
    fmt.Println(arr[4])  // 40

    var nums [3]int32 = [3]int32{5, 10, 15}
    var total int32 = sumByRef(&nums)
    fmt.Println(total)   // 30
}