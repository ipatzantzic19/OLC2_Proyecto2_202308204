.section .data
msg:
    .ascii "\n"
buffer:
    .ascii "0\n"
len = . - buffer

.str_0: .string "=== INICIO DE CALIFICACION: ESTRUCTURAS DE CONTROL ==="
.str_1: .string "%s"
.str_2: .string "\n"
.str_3: .string "Ana"
.str_4: .string "\n--- 2.2 IF ---"
.str_5: .string "El estudiante"
.str_6: .string "%s "
.str_7: .string "tiene una nota mayor a 80"
.str_8: .string "\n--- 2.3 IF ELSE ---"
.str_9: .string "Clasificacion: SOBRESALIENTE"
.str_10: .string "Clasificacion: EXCELENTE"
.str_11: .string "Clasificacion: REGULAR"
.str_12: .string "\n--- 2.4 SWITCH CASE DEFAULT ---"
.str_13: .string "Categoria 1: Principiante"
.str_14: .string "Categoria 2: Intermedio"
.str_15: .string "Categoria 3: Avanzado"
.str_16: .string "Categoria Desconocida"
.str_17: .string "\n--- 2.5 FOR CLASICO ---"
.str_18: .string "Iteracion:"
.str_19: .string "%ld"
.str_20: .string "\n--- 2.6 FOR CONDICIONAL ---"
.str_21: .string "Cuenta regresiva:"
.str_22: .string "\n--- 2.7 FOR INFINITO ---"
.str_23: .string "Intento:"
.str_24: .string "\n--- 2.8 BREAK ---"
.str_25: .string "Se encontro 7, se aplica break"
.str_26: .string "\n--- 2.9 CONTINUE ---"
.str_27: .string "Impar:"
.str_28: .string "\n=== FIN DE CALIFICACION: ESTRUCTURAS DE CONTROL ==="

.section .text
.align 2
.global _start

_start:
	stp x29, x30, [sp, #-16]!
	# guardar frame pointer y link register
	mov x29, sp
	# x29 = frame pointer (SP actual)
	sub sp, sp, #64
	# reservar 64 bytes para variables locales
	# frame.size=64, locals=8
	# string literal → x0 (puntero)
	adrp x0, .str_0
	add x0, x0, :lo12:.str_0
	# fmt.Println arg 0 (string)
	mov x1, x0
	# ptr string → x1
	adrp x0, .str_1
	add x0, x0, :lo12:.str_1
	bl printf
	adrp x0, .str_2
	add x0, x0, :lo12:.str_2
	bl printf
	# nota1 := expr (tipo inferido)
	mov x0, #85
	# int32 literal (64-bit per AArch64)
	str x0, [x29, #-8]
	# guardar int32 inferido (64-bit)
	# nota2 := expr (tipo inferido)
	mov x0, #92
	# int32 literal (64-bit per AArch64)
	str x0, [x29, #-16]
	# guardar int32 inferido (64-bit)
	# estudiante := expr (tipo inferido)
	# string literal → x0 (puntero)
	adrp x0, .str_3
	add x0, x0, :lo12:.str_3
	str x0, [x29, #-24]
	# guardar string inferido (64-bit)
	# string literal → x0 (puntero)
	adrp x0, .str_4
	add x0, x0, :lo12:.str_4
	# fmt.Println arg 0 (string)
	mov x1, x0
	# ptr string → x1
	adrp x0, .str_1
	add x0, x0, :lo12:.str_1
	bl printf
	adrp x0, .str_2
	add x0, x0, :lo12:.str_2
	bl printf
	# if condición #1
	ldr x0, [x29, #-8]
	# nota1 (int32 - 64-bit)
	mov x1, x0
	mov x0, #80
	# int32 literal (64-bit per AArch64)
	cmp x1, x0
	# comparar x1(lhs) vs x0(rhs) - flags setup
	b.le .if_end_0
	# branch falso (comparación simple)
	# string literal → x0 (puntero)
	adrp x0, .str_5
	add x0, x0, :lo12:.str_5
	# fmt.Println arg 0 (string)
	mov x1, x0
	# ptr string → x1
	adrp x0, .str_6
	add x0, x0, :lo12:.str_6
	bl printf
	ldr x0, [x29, #-24]
	# estudiante (string - 64-bit)
	# fmt.Println arg 1 (string)
	mov x1, x0
	# ptr string → x1
	adrp x0, .str_6
	add x0, x0, :lo12:.str_6
	bl printf
	# string literal → x0 (puntero)
	adrp x0, .str_7
	add x0, x0, :lo12:.str_7
	# fmt.Println arg 2 (string)
	mov x1, x0
	# ptr string → x1
	adrp x0, .str_1
	add x0, x0, :lo12:.str_1
	bl printf
	adrp x0, .str_2
	add x0, x0, :lo12:.str_2
	bl printf
	b .if_end_0
	# saltar al final del if
.if_end_0:
	# string literal → x0 (puntero)
	adrp x0, .str_8
	add x0, x0, :lo12:.str_8
	# fmt.Println arg 0 (string)
	mov x1, x0
	# ptr string → x1
	adrp x0, .str_1
	add x0, x0, :lo12:.str_1
	bl printf
	adrp x0, .str_2
	add x0, x0, :lo12:.str_2
	bl printf
	# if condición #1
	ldr x0, [x29, #-16]
	# nota2 (int32 - 64-bit)
	mov x1, x0
	mov x0, #95
	# int32 literal (64-bit per AArch64)
	cmp x1, x0
	# comparar x1(lhs) vs x0(rhs) - flags setup
	b.lt .else_branch_2
	# branch falso (comparación simple)
	# string literal → x0 (puntero)
	adrp x0, .str_9
	add x0, x0, :lo12:.str_9
	# fmt.Println arg 0 (string)
	mov x1, x0
	# ptr string → x1
	adrp x0, .str_1
	add x0, x0, :lo12:.str_1
	bl printf
	adrp x0, .str_2
	add x0, x0, :lo12:.str_2
	bl printf
	b .if_end_1
	# saltar al final del if
.else_branch_2:
	# else
	# if condición #1
	ldr x0, [x29, #-16]
	# nota2 (int32 - 64-bit)
	mov x1, x0
	mov x0, #90
	# int32 literal (64-bit per AArch64)
	cmp x1, x0
	# comparar x1(lhs) vs x0(rhs) - flags setup
	b.lt .else_branch_4
	# branch falso (comparación simple)
	# string literal → x0 (puntero)
	adrp x0, .str_10
	add x0, x0, :lo12:.str_10
	# fmt.Println arg 0 (string)
	mov x1, x0
	# ptr string → x1
	adrp x0, .str_1
	add x0, x0, :lo12:.str_1
	bl printf
	adrp x0, .str_2
	add x0, x0, :lo12:.str_2
	bl printf
	b .if_end_3
	# saltar al final del if
.else_branch_4:
	# else
	# string literal → x0 (puntero)
	adrp x0, .str_11
	add x0, x0, :lo12:.str_11
	# fmt.Println arg 0 (string)
	mov x1, x0
	# ptr string → x1
	adrp x0, .str_1
	add x0, x0, :lo12:.str_1
	bl printf
	adrp x0, .str_2
	add x0, x0, :lo12:.str_2
	bl printf
.if_end_3:
.if_end_1:
	# string literal → x0 (puntero)
	adrp x0, .str_12
	add x0, x0, :lo12:.str_12
	# fmt.Println arg 0 (string)
	mov x1, x0
	# ptr string → x1
	adrp x0, .str_1
	add x0, x0, :lo12:.str_1
	bl printf
	adrp x0, .str_2
	add x0, x0, :lo12:.str_2
	bl printf
	# codigoCategoria := expr (tipo inferido)
	mov x0, #2
	# int32 literal (64-bit per AArch64)
	str x0, [x29, #-32]
	# guardar int32 inferido (64-bit)
	# switch — evaluar expresión de control
	ldr x0, [x29, #-32]
	# codigoCategoria (int32 - 64-bit)
	mov x9, x0
	# valor del switch → x9
	# switch — tabla de comparaciones
	mov x0, #1
	# int32 literal (64-bit per AArch64)
	cmp x9, x0
	# comparar switch vs case[0]
	b.eq .sw_case_7
	# coincide → saltar al cuerpo
	mov x0, #2
	# int32 literal (64-bit per AArch64)
	cmp x9, x0
	# comparar switch vs case[1]
	b.eq .sw_case_8
	# coincide → saltar al cuerpo
	mov x0, #3
	# int32 literal (64-bit per AArch64)
	cmp x9, x0
	# comparar switch vs case[2]
	b.eq .sw_case_9
	# coincide → saltar al cuerpo
	b .sw_default_6
	# ningún case → default/end
.sw_case_7:
	# case[0] — cuerpo
	mov x0, #1
	# int32 literal (64-bit per AArch64)
	# string literal → x0 (puntero)
	adrp x0, .str_13
	add x0, x0, :lo12:.str_13
	# fmt.Println arg 0 (string)
	mov x1, x0
	# ptr string → x1
	adrp x0, .str_1
	add x0, x0, :lo12:.str_1
	bl printf
	adrp x0, .str_2
	add x0, x0, :lo12:.str_2
	bl printf
	b .sw_end_5
	# no fallthrough — saltar al final
.sw_case_8:
	# case[1] — cuerpo
	mov x0, #2
	# int32 literal (64-bit per AArch64)
	# string literal → x0 (puntero)
	adrp x0, .str_14
	add x0, x0, :lo12:.str_14
	# fmt.Println arg 0 (string)
	mov x1, x0
	# ptr string → x1
	adrp x0, .str_1
	add x0, x0, :lo12:.str_1
	bl printf
	adrp x0, .str_2
	add x0, x0, :lo12:.str_2
	bl printf
	b .sw_end_5
	# no fallthrough — saltar al final
.sw_case_9:
	# case[2] — cuerpo
	mov x0, #3
	# int32 literal (64-bit per AArch64)
	# string literal → x0 (puntero)
	adrp x0, .str_15
	add x0, x0, :lo12:.str_15
	# fmt.Println arg 0 (string)
	mov x1, x0
	# ptr string → x1
	adrp x0, .str_1
	add x0, x0, :lo12:.str_1
	bl printf
	adrp x0, .str_2
	add x0, x0, :lo12:.str_2
	bl printf
	b .sw_end_5
	# no fallthrough — saltar al final
.sw_default_6:
	# switch — default
	# string literal → x0 (puntero)
	adrp x0, .str_16
	add x0, x0, :lo12:.str_16
	# fmt.Println arg 0 (string)
	mov x1, x0
	# ptr string → x1
	adrp x0, .str_1
	add x0, x0, :lo12:.str_1
	bl printf
	adrp x0, .str_2
	add x0, x0, :lo12:.str_2
	bl printf
.sw_end_5:
	# string literal → x0 (puntero)
	adrp x0, .str_17
	add x0, x0, :lo12:.str_17
	# fmt.Println arg 0 (string)
	mov x1, x0
	# ptr string → x1
	adrp x0, .str_1
	add x0, x0, :lo12:.str_1
	bl printf
	adrp x0, .str_2
	add x0, x0, :lo12:.str_2
	bl printf
	# for init
	# i := expr (tipo inferido)
	mov x0, #1
	# int32 literal (64-bit per AArch64)
	str x0, [x29, #-40]
	# guardar int32 inferido (64-bit)
.for_start_10:
	# for condición
	ldr x0, [x29, #-40]
	# i (int32 - 64-bit)
	mov x1, x0
	mov x0, #5
	# int32 literal (64-bit per AArch64)
	cmp x1, x0
	# comparar x1(lhs) vs x0(rhs) - flags setup
	b.gt .for_end_11
	# falso salir del bucle (comparación simple)
	# string literal → x0 (puntero)
	adrp x0, .str_18
	add x0, x0, :lo12:.str_18
	# fmt.Println arg 0 (string)
	mov x1, x0
	# ptr string → x1
	adrp x0, .str_6
	add x0, x0, :lo12:.str_6
	bl printf
	ldr x0, [x29, #-40]
	# i (int32 - 64-bit)
	# fmt.Println arg 1 (int32)
	mov x1, x0
	# int32 → x1 para printf %ld
	adrp x0, .str_19
	add x0, x0, :lo12:.str_19
	bl printf
	adrp x0, .str_2
	add x0, x0, :lo12:.str_2
	bl printf
.for_post_12:
	# for post
	# i++
	ldr x0, [x29, #-40]
	# cargar i (int32)
	add x0, x0, #1
	# i + 1
	str x0, [x29, #-40]
	# guardar i++
	b .for_start_10
	# volver al test
.for_end_11:
	# string literal → x0 (puntero)
	adrp x0, .str_20
	add x0, x0, :lo12:.str_20
	# fmt.Println arg 0 (string)
	mov x1, x0
	# ptr string → x1
	adrp x0, .str_1
	add x0, x0, :lo12:.str_1
	bl printf
	adrp x0, .str_2
	add x0, x0, :lo12:.str_2
	bl printf
	# contador := expr (tipo inferido)
	mov x0, #10
	# int32 literal (64-bit per AArch64)
	str x0, [x29, #-48]
	# guardar int32 inferido (64-bit)
.while_start_13:
	# for-while condición
	ldr x0, [x29, #-48]
	# contador (int32 - 64-bit)
	mov x1, x0
	mov x0, xzr
	# int32 literal 0 (64-bit per AArch64)
	cmp x1, x0
	# comparar x1(lhs) vs x0(rhs) - flags setup
	b.le .while_end_14
	# falso salir (comparación simple)
	# string literal → x0 (puntero)
	adrp x0, .str_21
	add x0, x0, :lo12:.str_21
	# fmt.Println arg 0 (string)
	mov x1, x0
	# ptr string → x1
	adrp x0, .str_6
	add x0, x0, :lo12:.str_6
	bl printf
	ldr x0, [x29, #-48]
	# contador (int32 - 64-bit)
	# fmt.Println arg 1 (int32)
	mov x1, x0
	# int32 → x1 para printf %ld
	adrp x0, .str_19
	add x0, x0, :lo12:.str_19
	bl printf
	adrp x0, .str_2
	add x0, x0, :lo12:.str_2
	bl printf
	# contador -= expr
	ldr x0, [x29, #-48]
	# cargar int32 (64-bit)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	mov x0, #3
	# int32 literal (64-bit per AArch64)
	ldr x1, [sp]
	# lhs ← stack
	add sp, sp, #16
	sub x0, x1, x0
	# int32 resta
	str x0, [x29, #-48]
	# guardar int32 (64-bit)
	b .while_start_13
	# volver al test
.while_end_14:
	# string literal → x0 (puntero)
	adrp x0, .str_22
	add x0, x0, :lo12:.str_22
	# fmt.Println arg 0 (string)
	mov x1, x0
	# ptr string → x1
	adrp x0, .str_1
	add x0, x0, :lo12:.str_1
	bl printf
	adrp x0, .str_2
	add x0, x0, :lo12:.str_2
	bl printf
	# intentos := expr (tipo inferido)
	mov x0, xzr
	# int32 literal 0 (64-bit per AArch64)
	str x0, [x29, #-56]
	# guardar int32 inferido (64-bit)
.inf_start_15:
	# for infinito — cuerpo
	# intentos++
	ldr x0, [x29, #-56]
	# cargar intentos (int32)
	add x0, x0, #1
	# intentos + 1
	str x0, [x29, #-56]
	# guardar intentos++
	# string literal → x0 (puntero)
	adrp x0, .str_23
	add x0, x0, :lo12:.str_23
	# fmt.Println arg 0 (string)
	mov x1, x0
	# ptr string → x1
	adrp x0, .str_6
	add x0, x0, :lo12:.str_6
	bl printf
	ldr x0, [x29, #-56]
	# intentos (int32 - 64-bit)
	# fmt.Println arg 1 (int32)
	mov x1, x0
	# int32 → x1 para printf %ld
	adrp x0, .str_19
	add x0, x0, :lo12:.str_19
	bl printf
	adrp x0, .str_2
	add x0, x0, :lo12:.str_2
	bl printf
	# if condición #1
	ldr x0, [x29, #-56]
	# intentos (int32 - 64-bit)
	mov x1, x0
	mov x0, #3
	# int32 literal (64-bit per AArch64)
	cmp x1, x0
	# comparar x1(lhs) vs x0(rhs) - flags setup
	b.ne .if_end_17
	# branch falso (comparación simple)
	b .inf_end_16
	# break → salida del bucle/switch
	b .if_end_17
	# saltar al final del if
.if_end_17:
	b .inf_start_15
	# bucle infinito
.inf_end_16:
	# string literal → x0 (puntero)
	adrp x0, .str_24
	add x0, x0, :lo12:.str_24
	# fmt.Println arg 0 (string)
	mov x1, x0
	# ptr string → x1
	adrp x0, .str_1
	add x0, x0, :lo12:.str_1
	bl printf
	adrp x0, .str_2
	add x0, x0, :lo12:.str_2
	bl printf
	# for init
	# i := expr (tipo inferido)
	mov x0, #1
	# int32 literal (64-bit per AArch64)
	str x0, [x29, #-40]
	# guardar int32 inferido (64-bit)
.for_start_18:
	# for condición
	ldr x0, [x29, #-40]
	# i (int32 - 64-bit)
	mov x1, x0
	mov x0, #20
	# int32 literal (64-bit per AArch64)
	cmp x1, x0
	# comparar x1(lhs) vs x0(rhs) - flags setup
	b.gt .for_end_19
	# falso salir del bucle (comparación simple)
	# if condición #1
	ldr x0, [x29, #-40]
	# i (int32 - 64-bit)
	mov x1, x0
	mov x0, #7
	# int32 literal (64-bit per AArch64)
	cmp x1, x0
	# comparar x1(lhs) vs x0(rhs) - flags setup
	b.ne .if_end_21
	# branch falso (comparación simple)
	# string literal → x0 (puntero)
	adrp x0, .str_25
	add x0, x0, :lo12:.str_25
	# fmt.Println arg 0 (string)
	mov x1, x0
	# ptr string → x1
	adrp x0, .str_1
	add x0, x0, :lo12:.str_1
	bl printf
	adrp x0, .str_2
	add x0, x0, :lo12:.str_2
	bl printf
	b .for_end_19
	# break → salida del bucle/switch
	b .if_end_21
	# saltar al final del if
.if_end_21:
	ldr x0, [x29, #-40]
	# i (int32 - 64-bit)
	# fmt.Println arg 0 (int32)
	mov x1, x0
	# int32 → x1 para printf %ld
	adrp x0, .str_19
	add x0, x0, :lo12:.str_19
	bl printf
	adrp x0, .str_2
	add x0, x0, :lo12:.str_2
	bl printf
.for_post_20:
	# for post
	# i++
	ldr x0, [x29, #-40]
	# cargar i (int32)
	add x0, x0, #1
	# i + 1
	str x0, [x29, #-40]
	# guardar i++
	b .for_start_18
	# volver al test
.for_end_19:
	# string literal → x0 (puntero)
	adrp x0, .str_26
	add x0, x0, :lo12:.str_26
	# fmt.Println arg 0 (string)
	mov x1, x0
	# ptr string → x1
	adrp x0, .str_1
	add x0, x0, :lo12:.str_1
	bl printf
	adrp x0, .str_2
	add x0, x0, :lo12:.str_2
	bl printf
	# for init
	# j := expr (tipo inferido)
	mov x0, #1
	# int32 literal (64-bit per AArch64)
	str x0, [x29, #-64]
	# guardar int32 inferido (64-bit)
.for_start_22:
	# for condición
	ldr x0, [x29, #-64]
	# j (int32 - 64-bit)
	mov x1, x0
	mov x0, #6
	# int32 literal (64-bit per AArch64)
	cmp x1, x0
	# comparar x1(lhs) vs x0(rhs) - flags setup
	b.gt .for_end_23
	# falso salir del bucle (comparación simple)
	# if condición #1
	ldr x0, [x29, #-64]
	# j (int32 - 64-bit)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	mov x0, #2
	# int32 literal (64-bit per AArch64)
	ldr x1, [sp]
	# lhs ← stack (int32)
	add sp, sp, #16
	sdiv x2, x1, x0
	# x2 = lhs / rhs (cociente int32)
	msub x0, x2, x0, x1
	# x0 = lhs - cociente * rhs (resto int32)
	mov x1, x0
	mov x0, xzr
	# int32 literal 0 (64-bit per AArch64)
	cmp x1, x0
	# comparar x1(lhs) vs x0(rhs) - flags setup
	b.ne .if_end_25
	# branch falso (comparación simple)
	b .for_post_24
	# continue → siguiente iteración
	b .if_end_25
	# saltar al final del if
.if_end_25:
	# string literal → x0 (puntero)
	adrp x0, .str_27
	add x0, x0, :lo12:.str_27
	# fmt.Println arg 0 (string)
	mov x1, x0
	# ptr string → x1
	adrp x0, .str_6
	add x0, x0, :lo12:.str_6
	bl printf
	ldr x0, [x29, #-64]
	# j (int32 - 64-bit)
	# fmt.Println arg 1 (int32)
	mov x1, x0
	# int32 → x1 para printf %ld
	adrp x0, .str_19
	add x0, x0, :lo12:.str_19
	bl printf
	adrp x0, .str_2
	add x0, x0, :lo12:.str_2
	bl printf
.for_post_24:
	# for post
	# j++
	ldr x0, [x29, #-64]
	# cargar j (int32)
	add x0, x0, #1
	# j + 1
	str x0, [x29, #-64]
	# guardar j++
	b .for_start_22
	# volver al test
.for_end_23:
	# string literal → x0 (puntero)
	adrp x0, .str_28
	add x0, x0, :lo12:.str_28
	# fmt.Println arg 0 (string)
	mov x1, x0
	# ptr string → x1
	adrp x0, .str_1
	add x0, x0, :lo12:.str_1
	bl printf
	adrp x0, .str_2
	add x0, x0, :lo12:.str_2
	bl printf
	add sp, sp, #64
	# restaurar stack pointer
	ldp x29, x30, [sp], #16
	# restaurar frame pointer y link register
	mov x0, xzr
	# fflush(NULL)
	bl fflush
	# vaciar buffers stdio
	mov x0, #0
	# exit code = 0
	mov x8, #93
	# syscall exit
	svc #0
	# invoke