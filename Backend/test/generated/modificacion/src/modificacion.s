.section .data
msg:
    .ascii "\n"
buffer:
    .ascii "0\n"
len = . - buffer

.str_0: .string "%ld"
.str_1: .string "\n"

.section .text
.align 2
.global _start

_start:
	stp x29, x30, [sp, #-16]!
	# guardar frame pointer y link register
	mov x29, sp
	# x29 = frame pointer (SP actual)
	sub sp, sp, #16
	# reservar 16 bytes para variables locales
	# frame.size=16, locals=1
	# r := expr (tipo inferido)
	mov x0, #1
	# bool true = 1 (64-bit per AArch64)
	cbz x0, .tern_false_1
	# ternario: si falso → rama else
	# llamada a f (0 arg(s))
	bl f
	b .tern_end_0
	# ternario: salta al final
.tern_false_1:
	# llamada a g (0 arg(s))
	bl g
.tern_end_0:
	str x0, [x29, #-8]
	# guardar int32 inferido (64-bit)
	ldr x0, [x29, #-8]
	# r (int32 - 64-bit)
	# fmt.Println arg 0 (int32)
	mov x1, x0
	# int32 → x1 para printf %ld
	adrp x0, .str_0
	add x0, x0, :lo12:.str_0
	bl printf
	adrp x0, .str_1
	add x0, x0, :lo12:.str_1
	bl printf
.epilogue__start:
	add sp, sp, #16
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
f:
	stp x29, x30, [sp, #-16]!
	# guardar frame pointer y link register
	mov x29, sp
	# x29 = frame pointer (SP actual)
	# frame.size=0, locals=0
	mov x0, #1
	# int32 literal (64-bit per AArch64)
	# fmt.Println arg 0 (int32)
	mov x1, x0
	# int32 → x1 para printf %ld
	adrp x0, .str_0
	add x0, x0, :lo12:.str_0
	bl printf
	adrp x0, .str_1
	add x0, x0, :lo12:.str_1
	bl printf
	# return — valor único
	mov x0, #1
	# int32 literal (64-bit per AArch64)
	b .epilogue_f
	# return → epílogo
.epilogue_f:
	ldp x29, x30, [sp], #16
	ret
g:
	stp x29, x30, [sp, #-16]!
	# guardar frame pointer y link register
	mov x29, sp
	# x29 = frame pointer (SP actual)
	# frame.size=0, locals=0
	mov x0, #2
	# int32 literal (64-bit per AArch64)
	# fmt.Println arg 0 (int32)
	mov x1, x0
	# int32 → x1 para printf %ld
	adrp x0, .str_0
	add x0, x0, :lo12:.str_0
	bl printf
	adrp x0, .str_1
	add x0, x0, :lo12:.str_1
	bl printf
	# return — valor único
	mov x0, #2
	# int32 literal (64-bit per AArch64)
	b .epilogue_g
	# return → epílogo
.epilogue_g:
	ldp x29, x30, [sp], #16
	ret