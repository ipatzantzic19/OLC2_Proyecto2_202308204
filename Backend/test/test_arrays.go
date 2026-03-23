// ============================================================
//  PRUEBAS DE ARREGLOS – FASE 4 GOLAMPI
//  Archivo: test_arrays.golampi
// ============================================================

func main() {
    testArraySimple()
    testArrayInit()
    testArrayAssignment()
    testArrayTypes()
    testArrayMultiDim()
    testArrayLen()
    testArrayForLoop()
    testArrayAsParam()
    testArrayPointerParam()
}

// ── 1. Arreglo simple con valores por defecto ─────────────────
func testArraySimple() {
    fmt.Println("=== testArraySimple ===")

    var a [5]int32
    fmt.Println(a[0])   // 0
    fmt.Println(a[4])   // 0

    var b [3]bool
    fmt.Println(b[0])   // false

    var c [2]string
    fmt.Println(c[0])   // ""  (cadena vacía)
}

// ── 2. Arreglo con inicialización explícita ───────────────────
func testArrayInit() {
    fmt.Println("=== testArrayInit ===")

    var nums [3]int32 = [3]int32{10, 20, 30}
    fmt.Println(nums[0])  // 10
    fmt.Println(nums[1])  // 20
    fmt.Println(nums[2])  // 30

    var names [2]string = [2]string{"Ana", "Luis"}
    fmt.Println(names[0])  // Ana
    fmt.Println(names[1])  // Luis

    var flags [4]bool = [4]bool{true, false, true, false}
    fmt.Println(flags[0])  // true
    fmt.Println(flags[2])  // true
}

// ── 3. Asignación a elementos ─────────────────────────────────
func testArrayAssignment() {
    fmt.Println("=== testArrayAssignment ===")

    var nums [4]int32
    nums[0] = 10
    nums[1] = 20
    nums[2] = 30
    nums[3] = 40

    fmt.Println(nums[0])   // 10
    fmt.Println(nums[3])   // 40

    // Asignaciones compuestas
    nums[0] += 5
    fmt.Println(nums[0])   // 15

    nums[1] -= 5
    fmt.Println(nums[1])   // 15

    nums[2] *= 2
    fmt.Println(nums[2])   // 60

    nums[3] /= 4
    fmt.Println(nums[3])   // 10
}

// ── 4. Arreglos de distintos tipos ───────────────────────────
func testArrayTypes() {
    fmt.Println("=== testArrayTypes ===")

    var floats [3]float32 = [3]float32{1.1, 2.2, 3.3}
    fmt.Println(floats[0])  // 1.1
    fmt.Println(floats[2])  // 3.3

    var letters [3]rune = [3]rune{'a', 'b', 'c'}
    fmt.Println(letters[0])  // a
    fmt.Println(letters[1])  // b
}

// ── 5. Arreglos multidimensionales ───────────────────────────
func testArrayMultiDim() {
    fmt.Println("=== testArrayMultiDim ===")

    // Matriz 2x3 sin inicialización
    var grid [2][3]int32
    grid[0][0] = 1
    grid[0][1] = 2
    grid[0][2] = 3
    grid[1][0] = 4
    grid[1][1] = 5
    grid[1][2] = 6

    fmt.Println(grid[0][0])  // 1
    fmt.Println(grid[0][2])  // 3
    fmt.Println(grid[1][1])  // 5
    fmt.Println(grid[1][2])  // 6

    // Matriz 2x2 con inicialización
    var mat [2][2]int32 = [2][2]int32{
        {1, 2},
        {3, 4},
    }

    fmt.Println(mat[0][0])  // 1
    fmt.Println(mat[0][1])  // 2
    fmt.Println(mat[1][0])  // 3
    fmt.Println(mat[1][1])  // 4
}

// ── 6. Función len() con arreglos ────────────────────────────
func testArrayLen() {
    fmt.Println("=== testArrayLen ===")

    var a [4]int32
    fmt.Println(len(a))   // 4

    var b [10]bool
    fmt.Println(len(b))   // 10

    var s string = "Hola"
    fmt.Println(len(s))   // 4

    var m [2][3]int32
    fmt.Println(len(m))   // 2 (primera dimensión)
}

// ── 7. For con arreglos ───────────────────────────────────────
func testArrayForLoop() {
    fmt.Println("=== testArrayForLoop ===")

    var nums [5]int32 = [5]int32{10, 20, 30, 40, 50}

    // Suma de elementos
    var suma int32 = 0
    for i := 0; i < len(nums); i++ {
        suma += nums[i]
    }
    fmt.Println(suma)   // 150

    // Recorrido de matriz 2x2
    var m [2][2]int32 = [2][2]int32{
        {1, 2},
        {3, 4},
    }

    for i := 0; i < 2; i++ {
        for j := 0; j < 2; j++ {
            fmt.Println(m[i][j])
        }
    }
    // Output esperado: 1  2  3  4
}

// ── 8. Arreglo como parámetro (paso por valor) ───────────────
func sumArray(a [5]int32) int32 {
    var total int32 = 0
    for i := 0; i < 5; i++ {
        total += a[i]
    }
    return total
}

func sortBubble(a [5]int32) [5]int32 {
    for i := 0; i < 5; i++ {
        for j := 0; j < 4; j++ {
            if a[j] > a[j+1] {
                temp := a[j]
                a[j] = a[j+1]
                a[j+1] = temp
            }
        }
    }
    return a
}

func testArrayAsParam() {
    fmt.Println("=== testArrayAsParam ===")

    var nums [5]int32 = [5]int32{5, 3, 8, 1, 9}

    total := sumArray(nums)
    fmt.Println(total)   // 26

    nums = sortBubble(nums)
    fmt.Println(nums[0])  // 1
    fmt.Println(nums[1])  // 3
    fmt.Println(nums[2])  // 5
    fmt.Println(nums[3])  // 8
    fmt.Println(nums[4])  // 9
}

// ── 9. Arreglo por referencia (puntero) ───────────────────────
func fillArray(a *[5]int32) {
    for i := 0; i < 5; i++ {
        a[i] = i * 10
    }
}

func testArrayPointerParam() {
    fmt.Println("=== testArrayPointerParam ===")

    var arr [5]int32
    fillArray(&arr)

    fmt.Println(arr[0])  // 0
    fmt.Println(arr[1])  // 10
    fmt.Println(arr[2])  // 20
    fmt.Println(arr[3])  // 30
    fmt.Println(arr[4])  // 40
}