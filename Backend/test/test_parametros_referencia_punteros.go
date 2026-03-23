// ============================================================
//  PARÁMETROS POR REFERENCIA MEDIANTE PUNTEROS
//  Archivo: test_parametros_referencia_punteros.golampi
// ============================================================

// Ordenamiento SIN punteros (paso por valor)
// La función recibe una copia del arreglo
func sort(a [5]int32) [5]int32 {
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

// Ordenamiento CON punteros (paso por referencia)
// La función recibe la dirección del arreglo original
func sortRef(a *[5]int32) {
    for i := 0; i < 5; i++ {
        for j := 0; j < 4; j++ {
            if a[j] > a[j+1] {
                temp := a[j]
                a[j] = a[j+1]
                a[j+1] = temp
            }
        }
    }
}

func main() {
    nums1 := [5]int32{5, 3, 4, 1, 2}
    nums2 := [5]int32{5, 3, 4, 1, 2}

    // Aquí es obligatorio re-asignar, ya que sort trabaja sobre una copia
    nums1 = sort(nums1)

    // Aquí no se re-asigna, porque sortRef modifica el arreglo original
    sortRef(&nums2)

    fmt.Println(nums1[0], nums1[1], nums1[2], nums1[3], nums1[4])
    fmt.Println(nums2[0], nums2[1], nums2[2], nums2[3], nums2[4])
}

// Output esperado:
// 1 2 3 4 5
// 1 2 3 4 5
