.section .data
msg:
    .ascii "\n"
buffer:
    .ascii "0\n"
len = . - buffer

.section .text
.align 2
.global _start

_start:
	stp x29, x30, [sp, #-16]!
	# guardar frame pointer y link register
	mov x29, sp
	# x29 = frame pointer (SP actual)
	sub sp, sp, #32
	# reservar 32 bytes para variables locales
	# frame.size=32, locals=3
	# a := expr (tipo inferido)
	mov w0, #8
	# int32 literal (32-bit)
	str w0, [x29, #-8]
	# guardar int32 inferido (32-bit)
	# b := expr (tipo inferido)
	mov w0, #7
	# int32 literal (32-bit)
	str w0, [x29, #-16]
	# guardar int32 inferido (32-bit)
	# var result int32 (valor por defecto)
	mov w0, wzr
	# int32 default = 0 (32-bit)
	str w0, [x29, #-24]
	# if condición #1
	ldr w0, [x29, #-8]
	# a (int32 - 32-bit)
	mov w1, w0
	ldr w0, [x29, #-16]
	# b (int32 - 32-bit)
	cmp w1, w0
	# comparar w1(lhs) vs w0(rhs) - flags setup
	b.le .else_branch_1
	# branch falso (comparación simple)
	# result = expr
	ldr w0, [x29, #-8]
	# a (int32 - 32-bit)
	str w0, [x29, #-24]
	# guardar int32 (32-bit)
	b .if_end_0
	# saltar al final del if
.else_branch_1:
	# else
	# result = expr
	ldr w0, [x29, #-16]
	# b (int32 - 32-bit)
	str w0, [x29, #-24]
	# guardar int32 (32-bit)
.if_end_0:
	ldr w0, [x29, #-24]
	# result (int32 - 32-bit)
	# fmt.Println arg 0 (int32)
	add w3, w0, #48
	# convertir int a ASCII (w0 + 48 → w3)
	adrp x4, buffer
	add x4, x4, :lo12:buffer
	strb w3, [x4]
	# guardar ASCII en buffer[0]
	mov x0, #1
	# fd = stdout
	mov x1, x4
	# buffer
	mov x2, #2
	# length = 2 (digit + newline)
	mov x8, #64
	# syscall write
	svc #0
	# invoke
	add sp, sp, #32
	# restaurar stack pointer
	ldp x29, x30, [sp], #16
	# restaurar frame pointer y link register
	mov x0, #0
	# exit code = 0
	mov x8, #93
	# syscall exit
	svc #0
	# invoke