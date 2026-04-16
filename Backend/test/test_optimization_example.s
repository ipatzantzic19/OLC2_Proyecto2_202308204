.section .data
.str_0: .string "%d"
.str_1: .string "\n"

.section .text
.global main

main:
	stp x29, x30, [sp, #-16]!
	mov x29, sp
	# frame.size=0, locals=1
	# a := expr (tipo inferido)
	mov x0, #8
	# b := expr (tipo inferido)
	mov x0, #7
	# var result int32 (valor por defecto)
	# if condición #1
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal
	ldr x1, [sp]
	# lhs ← stack
	add sp, sp, #16
	cmp x1, x0
	# comparar lhs vs rhs
	cset x0, gt
	# bool resultado (>)
	cbz x0, .else_branch_1
	# falso → siguiente rama
	# result = expr
	b .if_end_0
	# saltar al final del if
.else_branch_1:
	# else
	# result = expr
.if_end_0:
	# fmt.Println arg 0 (int32)
	mov x1, x0
	# int32 → x1 para printf
	adrp x0, .str_0
	add x0, x0, :lo12:.str_0
	bl printf
	adrp x0, .str_1
	add x0, x0, :lo12:.str_1
	bl printf
	add sp, sp, #16
	ldp x29, x30, [sp], #16
	ret