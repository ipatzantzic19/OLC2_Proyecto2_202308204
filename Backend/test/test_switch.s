# ============================================================
# Golampi Compiler — Fase 2 — ARM64 (AArch64)
# Compilar:
#   aarch64-linux-gnu-gcc -o programa program.s -lc
# Ejecutar:
#   qemu-aarch64 -L /usr/aarch64-linux-gnu ./programa
# ============================================================

.section .data
.str_0: .string "%d"
.str_1: .string "\n"
.str_2: .string ""
.str_3: .string "Hola"
.str_4: .string "%s"

.section .text
.global _start


_start:
	# ── función main ── registro de activación ──
	stp x29, x30, [sp, #-16]!                  # guardar fp (enlace control) y lr
	mov x29, sp                                # establecer frame pointer
	sub sp, sp, #16                            # reservar 16 bytes (locales + params)
	# day := expr (tipo inferido)
	mov x0, #2
	str x0, [x29, #-8]                         # guardar int32 inferido
	# switch — evaluar expresión de control
	ldr x0, [x29, #-8]                         # day (int32)
	mov x19, x0                                # valor del switch → x19 (callee-saved)
	# switch — tabla de comparaciones
	mov x0, #1
	cmp x19, x0                                # comparar switch vs case[0]
	b.eq .sw_case_2                            # coincide → saltar al cuerpo
	mov x0, #2
	cmp x19, x0                                # comparar switch vs case[1]
	b.eq .sw_case_3                            # coincide → saltar al cuerpo
	mov x0, #3
	cmp x19, x0                                # comparar switch vs case[2]
	b.eq .sw_case_4                            # coincide → saltar al cuerpo
	b .sw_default_1                            # ningún case → default/end
.sw_case_2:
	# case[0] — cuerpo
	mov x0, #1
	mov x0, #100
	# fmt.Println arg 0 (int32)
	mov x1, x0                                 # int32 → x1 para printf
	adrp x0, .str_0
	add x0, x0, :lo12:.str_0
	bl printf
	adrp x0, .str_1
	add x0, x0, :lo12:.str_1
	bl printf
	b .sw_end_0                                # no fallthrough — saltar al final
.sw_case_3:
	# case[1] — cuerpo
	mov x0, #2
	mov x0, #200
	# fmt.Println arg 0 (int32)
	mov x1, x0                                 # int32 → x1 para printf
	adrp x0, .str_0
	add x0, x0, :lo12:.str_0
	bl printf
	adrp x0, .str_1
	add x0, x0, :lo12:.str_1
	bl printf
	b .sw_end_0                                # no fallthrough — saltar al final
.sw_case_4:
	# case[2] — cuerpo
	mov x0, #3
	mov x0, #300
	# fmt.Println arg 0 (int32)
	mov x1, x0                                 # int32 → x1 para printf
	adrp x0, .str_0
	add x0, x0, :lo12:.str_0
	bl printf
	adrp x0, .str_1
	add x0, x0, :lo12:.str_1
	bl printf
	b .sw_end_0                                # no fallthrough — saltar al final
.sw_default_1:
	# switch — default
	mov x0, #999
	# fmt.Println arg 0 (int32)
	mov x1, x0                                 # int32 → x1 para printf
	adrp x0, .str_0
	add x0, x0, :lo12:.str_0
	bl printf
	adrp x0, .str_1
	add x0, x0, :lo12:.str_1
	bl printf
.sw_end_0:
	# var message string (valor por defecto)
	adrp x0, .str_2                            # string default = ""
	add x0, x0, :lo12:.str_2
	str x0, [x29, #-16]
	# message = expr
	# string literal → x0 (puntero)
	adrp x0, .str_3
	add x0, x0, :lo12:.str_3
	str x0, [x29, #-16]                        # guardar string
	ldr x0, [x29, #-16]                        # message (string)
	# fmt.Println arg 0 (string)
	mov x1, x0                                 # ptr string → x1
	adrp x0, .str_4
	add x0, x0, :lo12:.str_4
	bl printf
	adrp x0, .str_1
	add x0, x0, :lo12:.str_1
	bl printf
.epilogue__start:
	# ── epílogo main ──
	add sp, sp, #16                            # liberar espacio de locales
	ldp x29, x30, [sp], #16                    # restaurar fp y lr
	mov x0, #0                                 # exit code 0
	ret