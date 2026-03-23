# Gramática formal de Golampi

Sea la gramática:

G = (V, T, P, S)

## V = No terminales

V = {
program,
declarationList, declaration,
varDeclaration, shortVarDeclaration, constDeclaration,
functionDeclaration, functionReturn,
parameterListOpt, parameterList, parameterTail, parameter,
typeList, typeTail,
idList, idTail,
expressionList, expressionTail,
statement,
assignment, assignOp, indexChain,
incDecStatement,
ifStatement, elseIfList, elseOpt,
switchStatement, caseClauseList, caseClause, defaultClauseOpt, defaultClause,
statementList,
forStatement, forClause, forInitOpt, forInit, forPostOpt, forPost, expressionOpt,
breakStatement, continueStatement, returnStatement, expressionListOpt,
block, expressionStatement,
expression, logicalOr, logicalOrTail,
logicalAnd, logicalAndTail,
equality, equalityTail,
relational, relationalTail,
additive, additiveTail,
multiplicative, multiplicativeTail,
unary, primary,
primaryIdSuffix,
arrayLiteral, arrayLiteralContentOpt,
innerLiteralList, innerLiteralTail, innerLiteral, commaOpt,
argumentListOpt, argumentList, argumentTail, argument,
type
}

## T = Terminales

T = {
VAR, CONST, FUNC, IF, ELSE, SWITCH, CASE, DEFAULT, FOR, BREAK, CONTINUE, RETURN,
TRUE, FALSE, NIL,
INT32_TYPE, FLOAT32_TYPE, BOOL_TYPE, RUNE_TYPE, STRING_TYPE,
INT32, FLOAT32, RUNE, STRING, ID,
AND, OR,
"=", "+=", "-=", "*=", "/=",
"==", "!=", ">", ">=", "<", "<=",
"+", "-", "*", "/", "%", "!", "&",
"++", "--", ":=",
"(", ")", "[", "]", "{", "}", ",", ".", ";", ":",
EOF
}

## S = Símbolo de inicio

S = program

## P = Producciones

1.  program ::= declarationList EOF

2.  declarationList ::= declaration declarationList | ε

3.  declaration ::= varDeclaration
                 | constDeclaration
                 | functionDeclaration
                 | statement

4.  varDeclaration ::= VAR idList type
                    | VAR idList type "=" expressionList

5.  shortVarDeclaration ::= idList ":=" expressionList

6.  constDeclaration ::= CONST ID type "=" expression

7.  functionDeclaration ::= FUNC ID "(" parameterListOpt ")" functionReturn block

8.  functionReturn ::= type
                    | "(" typeList ")"
                    | ε

9.  parameterListOpt ::= parameterList | ε

10. parameterList ::= parameter parameterTail

11. parameterTail ::= "," parameter parameterTail | ε

12. parameter ::= ID type
               | "*" ID type

13. typeList ::= type typeTail

14. typeTail ::= "," type typeTail | ε

15. idList ::= ID idTail

16. idTail ::= "," ID idTail | ε

17. expressionList ::= expression expressionTail

18. expressionTail ::= "," expression expressionTail | ε

19. statement ::= shortVarDeclaration
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

20. assignment ::= ID assignOp expression
                 | ID indexChain assignOp expression
                 | "*" ID assignOp expression

21. assignOp ::= "=" | "+=" | "-=" | "*=" | "/="

22. indexChain ::= "[" expression "]" indexChain
                 | "[" expression "]"

23. incDecStatement ::= ID "++" | ID "--"

24. ifStatement ::= IF expression block elseIfList elseOpt

25. elseIfList ::= ELSE IF expression block elseIfList | ε

26. elseOpt ::= ELSE block | ε

27. switchStatement ::= SWITCH expression "{" caseClauseList defaultClauseOpt "}"

28. caseClauseList ::= caseClause caseClauseList | ε

29. caseClause ::= CASE expressionList ":" statementList

30. defaultClauseOpt ::= defaultClause | ε

31. defaultClause ::= DEFAULT ":" statementList

32. statementList ::= statement statementList | ε

33. forStatement ::= FOR forClause block
                   | FOR expression block
                   | FOR block

34. forClause ::= forInitOpt ";" expressionOpt ";" forPostOpt

35. forInitOpt ::= forInit | ε

36. forInit ::= varDeclaration
             | shortVarDeclaration
             | assignment
             | incDecStatement

37. forPostOpt ::= forPost | ε

38. forPost ::= assignment | incDecStatement

39. expressionOpt ::= expression | ε

40. breakStatement ::= BREAK

41. continueStatement ::= CONTINUE

42. returnStatement ::= RETURN expressionListOpt

43. expressionListOpt ::= expressionList | ε

44. block ::= "{" declarationList "}"

45. expressionStatement ::= expression

46. expression ::= logicalOr

47. logicalOr ::= logicalAnd logicalOrTail

48. logicalOrTail ::= OR logicalAnd logicalOrTail | ε

49. logicalAnd ::= equality logicalAndTail

50. logicalAndTail ::= AND equality logicalAndTail | ε

51. equality ::= relational equalityTail

52. equalityTail ::= "==" relational equalityTail
                  | "!=" relational equalityTail
                  | ε

53. relational ::= additive relationalTail

54. relationalTail ::= ">" additive relationalTail
                    | ">=" additive relationalTail
                    | "<" additive relationalTail
                    | "<=" additive relationalTail
                    | ε

55. additive ::= multiplicative additiveTail

56. additiveTail ::= "+" multiplicative additiveTail
                  | "-" multiplicative additiveTail
                  | ε

57. multiplicative ::= unary multiplicativeTail

58. multiplicativeTail ::= "*" unary multiplicativeTail
                        | "/" unary multiplicativeTail
                        | "%" unary multiplicativeTail
                        | ε

59. unary ::= primary
            | "-" unary
            | "!" unary
            | "&" ID
            | "*" unary

60. primary ::= INT32
              | FLOAT32
              | RUNE
              | STRING
              | TRUE
              | FALSE
              | NIL
              | ID primaryIdSuffix
              | ID indexChain
              | ID
              | "(" expression ")"
              | arrayLiteral
              | "{" expressionList "}"

61. primaryIdSuffix ::= "." ID "(" argumentListOpt ")"
                      | "(" argumentListOpt ")"

62. argumentListOpt ::= argumentList | ε

63. argumentList ::= argument argumentTail

64. argumentTail ::= "," argument argumentTail | ε

65. argument ::= expression | "&" ID

66. arrayLiteral ::= "[" expression "]" type "{" arrayLiteralContentOpt "}"

67. arrayLiteralContentOpt ::= expressionList
                             | innerLiteralList
                             | ε

68. innerLiteralList ::= innerLiteral innerLiteralTail commaOpt

69. innerLiteralTail ::= "," innerLiteral innerLiteralTail | ε

70. innerLiteral ::= "{" expressionList commaOpt "}"

71. commaOpt ::= "," | ε

72. type ::= INT32_TYPE
           | FLOAT32_TYPE
           | BOOL_TYPE
           | RUNE_TYPE
           | STRING_TYPE
           | "[" expression "]" type
           | "*" type
