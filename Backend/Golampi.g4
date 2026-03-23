grammar Golampi;

// ==================== PROGRAMA ====================
program
    : declaration* EOF
    ;

// ==================== DECLARACIONES ====================
declaration
    : varDeclaration
    | constDeclaration
    | functionDeclaration
    | statement
    ;

// ==================== VARIABLES ====================
varDeclaration
    : VAR idList type                                     # VarDeclSimple
    | VAR idList type '=' expressionList                  # VarDeclWithInit
    ;

shortVarDeclaration
    : idList ':=' expressionList                          # ShortVarDecl
    ;

// ==================== CONSTANTES ====================
constDeclaration
    : CONST ID type '=' expression                        # ConstDecl
    ;

// ==================== FUNCIONES ====================
functionDeclaration
    : FUNC ID '(' parameterList? ')' type? block          # FuncDeclSingleReturn
    | FUNC ID '(' parameterList? ')' '(' typeList ')' block # FuncDeclMultiReturn
    ;

parameterList
    : parameter (',' parameter)*
    ;

parameter
    : ID type                                             # NormalParameter
    | '*' ID type                                         # PointerParameter
    ;

typeList
    : type (',' type)*
    ;

idList
    : ID (',' ID)*
    ;

expressionList
    : expression (',' expression)*
    ;

// ==================== SENTENCIAS ====================
statement
    : shortVarDeclaration
    | assignment
    | ifStatement
    | switchStatement
    | forStatement
    | breakStatement
    | continueStatement
    | returnStatement
    | incDecStatement
    | block
    | expressionStatement
    ;

// ==================== ASIGNACIONES ====================
assignment
    : ID assignOp expression                              # SimpleAssignment
    | ID ('[' expression ']')+ assignOp expression        # ArrayAssignment
    | '*' ID assignOp expression                          # PointerAssignment
    ;

assignOp
    : '=' | '+=' | '-=' | '*=' | '/='
    ;

// ==================== INCREMENTO/DECREMENTO ====================
incDecStatement
    : ID '++'                                             # IncrementStatement
    | ID '--'                                             # DecrementStatement
    ;

// ==================== CONTROL DE FLUJO ====================
ifStatement
    : IF expression block (ELSE IF expression block)* (ELSE block)?  # IfElseIfElse
    ;

switchStatement
    : SWITCH expression '{' caseClause* defaultClause? '}'
    ;

caseClause
    : CASE expressionList ':' statement*
    ;

defaultClause
    : DEFAULT ':' statement*
    ;

forStatement
    : FOR forClause block                                 # ForTraditional
    | FOR expression block                                # ForWhile
    | FOR block                                           # ForInfinite
    ;

forClause
    : forInit ';' expression? ';' forPost
    ;

forInit
    : varDeclaration
    | shortVarDeclaration
    | assignment
    | incDecStatement
    | /* empty */
    ;

forPost
    : assignment
    | incDecStatement
    | /* empty */
    ;

breakStatement
    : BREAK
    ;

continueStatement
    : CONTINUE
    ;

returnStatement
    : RETURN expressionList?
    ;

// ==================== BLOQUES ====================
block
    : '{' declaration* '}'
    ;

expressionStatement
    : expression
    ;

// ==================== EXPRESIONES ====================
expression
    : logicalOr
    ;

logicalOr
    : logicalAnd (OR logicalAnd)*
    ;

logicalAnd
    : equality (AND equality)*
    ;

equality
    : relational (('==' | '!=') relational)*
    ;

relational
    : additive (('>' | '>=' | '<' | '<=') additive)*
    ;

additive
    : multiplicative (('+' | '-') multiplicative)*
    ;

multiplicative
    : unary (('*' | '/' | '%') unary)*
    ;

unary
    : primary                                             # PrimaryUnary
    | '-' unary                                           # NegativeUnary
    | '!' unary                                           # NotUnary
    | '&' ID                                              # AddressOf
    | '*' unary                                           # Dereference
    ;

// ==================== PRIMARIOS ====================
primary
    : INT32                                               # IntLiteral
    | FLOAT32                                             # FloatLiteral
    | RUNE                                                # RuneLiteral
    | STRING                                              # StringLiteral
    | TRUE                                                # TrueLiteral
    | FALSE                                               # FalseLiteral
    | NIL                                                 # NilLiteral
    | ID ('.' ID)? '(' argumentList? ')'                  # FunctionCall
    | ID ('[' expression ']')+                            # ArrayAccess
    | ID                                                  # Identifier
    | '(' expression ')'                                  # GroupedExpression
    | arrayLiteral                                        # ArrayLiteralExpr
    | '{' expressionList '}'                              # InnerArrayLiteral
    ;

// ==================== LITERALES DE ARREGLO ====================
arrayLiteral
    : '[' expression ']' type '{' (expressionList | innerLiteralList)? '}'  # FixedArrayLiteralNode
    | '[' ']' type '{' expressionList? '}'                                  # SliceLiteralNode
    ;

innerLiteralList
    : innerLiteral (',' innerLiteral)* ','? 
    ;

innerLiteral
    : '{' expressionList ','? '}'
    ;

argumentList
    : argument (',' argument)*
    ;

argument
    : expression                                          # ExpressionArgument
    | '&' ID                                              # AddressArgument
    ;

// ==================== TIPOS ====================
type
    : INT32_TYPE                                          # Int32Type
    | FLOAT32_TYPE                                        # Float32Type
    | BOOL_TYPE                                           # BoolType
    | RUNE_TYPE                                           # RuneType
    | STRING_TYPE                                         # StringType
    | '[' expression ']' type                             # ArrayType
    | '[' ']' type                                        # SliceType
    | '*' type                                            # PointerType
    ;

// ==================== PALABRAS RESERVADAS ====================
VAR      : 'var';
CONST    : 'const';
FUNC     : 'func';
IF       : 'if';
ELSE     : 'else';
SWITCH   : 'switch';
CASE     : 'case';
DEFAULT  : 'default';
FOR      : 'for';
BREAK    : 'break';
CONTINUE : 'continue';
RETURN   : 'return';
TRUE     : 'true';
FALSE    : 'false';
NIL      : 'nil';

// ==================== TIPOS PRIMITIVOS ====================
INT32_TYPE   : 'int32';
FLOAT32_TYPE : 'float32';
BOOL_TYPE    : 'bool';
RUNE_TYPE    : 'rune';
STRING_TYPE  : 'string';

// ==================== OPERADORES LÓGICOS ====================
AND : '&&';
OR  : '||';

// ==================== LITERALES ====================
INT32   : [0-9]+;
FLOAT32 : [0-9]+ '.' [0-9]+;
RUNE    : '\'' (~['\\] | '\\' .) '\'';
STRING  : '"' (~["\\] | '\\' .)* '"';
ID      : [a-zA-Z_][a-zA-Z0-9_]*;

// ==================== COMENTARIOS Y ESPACIOS ====================
LINE_COMMENT  : '//' ~[\r\n]* -> skip;
BLOCK_COMMENT : '/*' .*? '*/' -> skip;
WS            : [ \t\r\n]+ -> skip;