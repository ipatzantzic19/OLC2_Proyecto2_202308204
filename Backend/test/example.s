.section .data
msg:
    .ascii "\n"
buffer:
    .ascii "0\n"
len = . - buffer

.str_0: .string "%ld"

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
	mov x0, #8
	# int32 literal (64-bit per AArch64)
	str x0, [x29, #-8]
	# guardar int32 inferido (64-bit)
	# b := expr (tipo inferido)
	mov x0, #7
	# int32 literal (64-bit per AArch64)
	str x0, [x29, #-16]
	# guardar int32 inferido (64-bit)
	# var result int32 (valor por defecto)
	mov x0, xzr
	# int32 default = 0 (64-bit)
	str x0, [x29, #-24]
	# if condición #1
	ldr x0, [x29, #-8]
	# a (int32 - 64-bit)
	mov x1, x0
	ldr x0, [x29, #-16]
	# b (int32 - 64-bit)
	cmp x1, x0
	# comparar x1(lhs) vs x0(rhs) - flags setup
	b.le .else_branch_1
	# branch falso (comparación simple)
	# result = expr
	ldr x0, [x29, #-8]
	# a (int32 - 64-bit)
	str x0, [x29, #-24]
	# guardar int32 (64-bit)
	b .if_end_0
	# saltar al final del if
.else_branch_1:
	# else
	# result = expr
	ldr x0, [x29, #-16]
	# b (int32 - 64-bit)
	str x0, [x29, #-24]
	# guardar int32 (64-bit)
.if_end_0:
	ldr x0, [x29, #-24]
	# result (int32 - 64-bit)
	# fmt.Println arg 0 (int32)
	mov x1, x0
	# int32 → x1 para printf %ld
	adrp x0, .str_0
	add x0, x0, :lo12:.str_0
	bl printf
	adrp x4, msg
	add x4, x4, :lo12:msg
	mov x0, #1
	# fd = stdout
	mov x1, x4
	# buffer = msg
	mov x2, #1
	# length = 1 (just newline)
	mov x8, #64
	# syscall write
	svc #0
	# invoke
	add sp, sp, #32
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