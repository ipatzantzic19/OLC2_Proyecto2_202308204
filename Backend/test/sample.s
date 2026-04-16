.section .data
.align 2
.flt_0: .single 5.0
.align 2
.flt_1: .single 2.0
.str_0: .string "%g"
.str_1: .string "\n"

.section .text
.global _start

_start:
	stp x29, x30, [sp, #-16]!
	mov x29, sp
	# frame.size=0, locals=0
	# a := expr (tipo inferido)
	# float32 literal 5.0 → s0
	adrp x9, .flt_0
	# página de la constante float
	ldr s0, [x9, :lo12:.flt_0]
	# cargar float32 IEEE-754 en s0
	str s0, [x29, #-8]
	# guardar float32 inferido
	# b := expr (tipo inferido)
	# float32 literal 2.0 → s0
	adrp x9, .flt_1
	# página de la constante float
	ldr s0, [x9, :lo12:.flt_1]
	# cargar float32 IEEE-754 en s0
	str s0, [x29, #-16]
	# guardar float32 inferido
	# c := expr (tipo inferido)
	ldr s0, [x29, #-8]
	# a (float32)
	sub sp, sp, #16
	# reservar slot float temporal
	str s0, [sp]
	# s0 stack temporal
	ldr s0, [x29, #-16]
	# b (float32)
	ldr s1, [sp]
	# stack s1 (lhs float)
	add sp, sp, #16
	fsub s0, s1, s0
	# float32 resta
	str s0, [x29, #-24]
	# guardar float32 inferido
	ldr s0, [x29, #-24]
	# c (float32)
	# fmt.Println arg 0 (float32)
	fcvt d0, s0
	# float32 a float64 para printf variadic
	adrp x0, .str_0
	add x0, x0, :lo12:.str_0
	bl printf
	adrp x0, .str_1
	add x0, x0, :lo12:.str_1
	bl printf
	add sp, sp, #32
	ldp x29, x30, [sp], #16
	ret