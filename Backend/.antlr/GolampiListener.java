// Generated from /home/isai/Documentos/Github/OLC2_Proyecto1_202308204/Backend/Golampi.g4 by ANTLR 4.13.1
import org.antlr.v4.runtime.tree.ParseTreeListener;

/**
 * This interface defines a complete listener for a parse tree produced by
 * {@link GolampiParser}.
 */
public interface GolampiListener extends ParseTreeListener {
	/**
	 * Enter a parse tree produced by {@link GolampiParser#program}.
	 * @param ctx the parse tree
	 */
	void enterProgram(GolampiParser.ProgramContext ctx);
	/**
	 * Exit a parse tree produced by {@link GolampiParser#program}.
	 * @param ctx the parse tree
	 */
	void exitProgram(GolampiParser.ProgramContext ctx);
	/**
	 * Enter a parse tree produced by {@link GolampiParser#declaration}.
	 * @param ctx the parse tree
	 */
	void enterDeclaration(GolampiParser.DeclarationContext ctx);
	/**
	 * Exit a parse tree produced by {@link GolampiParser#declaration}.
	 * @param ctx the parse tree
	 */
	void exitDeclaration(GolampiParser.DeclarationContext ctx);
	/**
	 * Enter a parse tree produced by the {@code VarDeclSimple}
	 * labeled alternative in {@link GolampiParser#varDeclaration}.
	 * @param ctx the parse tree
	 */
	void enterVarDeclSimple(GolampiParser.VarDeclSimpleContext ctx);
	/**
	 * Exit a parse tree produced by the {@code VarDeclSimple}
	 * labeled alternative in {@link GolampiParser#varDeclaration}.
	 * @param ctx the parse tree
	 */
	void exitVarDeclSimple(GolampiParser.VarDeclSimpleContext ctx);
	/**
	 * Enter a parse tree produced by the {@code VarDeclWithInit}
	 * labeled alternative in {@link GolampiParser#varDeclaration}.
	 * @param ctx the parse tree
	 */
	void enterVarDeclWithInit(GolampiParser.VarDeclWithInitContext ctx);
	/**
	 * Exit a parse tree produced by the {@code VarDeclWithInit}
	 * labeled alternative in {@link GolampiParser#varDeclaration}.
	 * @param ctx the parse tree
	 */
	void exitVarDeclWithInit(GolampiParser.VarDeclWithInitContext ctx);
	/**
	 * Enter a parse tree produced by the {@code ShortVarDecl}
	 * labeled alternative in {@link GolampiParser#shortVarDeclaration}.
	 * @param ctx the parse tree
	 */
	void enterShortVarDecl(GolampiParser.ShortVarDeclContext ctx);
	/**
	 * Exit a parse tree produced by the {@code ShortVarDecl}
	 * labeled alternative in {@link GolampiParser#shortVarDeclaration}.
	 * @param ctx the parse tree
	 */
	void exitShortVarDecl(GolampiParser.ShortVarDeclContext ctx);
	/**
	 * Enter a parse tree produced by the {@code ConstDecl}
	 * labeled alternative in {@link GolampiParser#constDeclaration}.
	 * @param ctx the parse tree
	 */
	void enterConstDecl(GolampiParser.ConstDeclContext ctx);
	/**
	 * Exit a parse tree produced by the {@code ConstDecl}
	 * labeled alternative in {@link GolampiParser#constDeclaration}.
	 * @param ctx the parse tree
	 */
	void exitConstDecl(GolampiParser.ConstDeclContext ctx);
	/**
	 * Enter a parse tree produced by the {@code FuncDeclSingleReturn}
	 * labeled alternative in {@link GolampiParser#functionDeclaration}.
	 * @param ctx the parse tree
	 */
	void enterFuncDeclSingleReturn(GolampiParser.FuncDeclSingleReturnContext ctx);
	/**
	 * Exit a parse tree produced by the {@code FuncDeclSingleReturn}
	 * labeled alternative in {@link GolampiParser#functionDeclaration}.
	 * @param ctx the parse tree
	 */
	void exitFuncDeclSingleReturn(GolampiParser.FuncDeclSingleReturnContext ctx);
	/**
	 * Enter a parse tree produced by the {@code FuncDeclMultiReturn}
	 * labeled alternative in {@link GolampiParser#functionDeclaration}.
	 * @param ctx the parse tree
	 */
	void enterFuncDeclMultiReturn(GolampiParser.FuncDeclMultiReturnContext ctx);
	/**
	 * Exit a parse tree produced by the {@code FuncDeclMultiReturn}
	 * labeled alternative in {@link GolampiParser#functionDeclaration}.
	 * @param ctx the parse tree
	 */
	void exitFuncDeclMultiReturn(GolampiParser.FuncDeclMultiReturnContext ctx);
	/**
	 * Enter a parse tree produced by {@link GolampiParser#parameterList}.
	 * @param ctx the parse tree
	 */
	void enterParameterList(GolampiParser.ParameterListContext ctx);
	/**
	 * Exit a parse tree produced by {@link GolampiParser#parameterList}.
	 * @param ctx the parse tree
	 */
	void exitParameterList(GolampiParser.ParameterListContext ctx);
	/**
	 * Enter a parse tree produced by the {@code NormalParameter}
	 * labeled alternative in {@link GolampiParser#parameter}.
	 * @param ctx the parse tree
	 */
	void enterNormalParameter(GolampiParser.NormalParameterContext ctx);
	/**
	 * Exit a parse tree produced by the {@code NormalParameter}
	 * labeled alternative in {@link GolampiParser#parameter}.
	 * @param ctx the parse tree
	 */
	void exitNormalParameter(GolampiParser.NormalParameterContext ctx);
	/**
	 * Enter a parse tree produced by the {@code PointerParameter}
	 * labeled alternative in {@link GolampiParser#parameter}.
	 * @param ctx the parse tree
	 */
	void enterPointerParameter(GolampiParser.PointerParameterContext ctx);
	/**
	 * Exit a parse tree produced by the {@code PointerParameter}
	 * labeled alternative in {@link GolampiParser#parameter}.
	 * @param ctx the parse tree
	 */
	void exitPointerParameter(GolampiParser.PointerParameterContext ctx);
	/**
	 * Enter a parse tree produced by {@link GolampiParser#typeList}.
	 * @param ctx the parse tree
	 */
	void enterTypeList(GolampiParser.TypeListContext ctx);
	/**
	 * Exit a parse tree produced by {@link GolampiParser#typeList}.
	 * @param ctx the parse tree
	 */
	void exitTypeList(GolampiParser.TypeListContext ctx);
	/**
	 * Enter a parse tree produced by {@link GolampiParser#idList}.
	 * @param ctx the parse tree
	 */
	void enterIdList(GolampiParser.IdListContext ctx);
	/**
	 * Exit a parse tree produced by {@link GolampiParser#idList}.
	 * @param ctx the parse tree
	 */
	void exitIdList(GolampiParser.IdListContext ctx);
	/**
	 * Enter a parse tree produced by {@link GolampiParser#expressionList}.
	 * @param ctx the parse tree
	 */
	void enterExpressionList(GolampiParser.ExpressionListContext ctx);
	/**
	 * Exit a parse tree produced by {@link GolampiParser#expressionList}.
	 * @param ctx the parse tree
	 */
	void exitExpressionList(GolampiParser.ExpressionListContext ctx);
	/**
	 * Enter a parse tree produced by {@link GolampiParser#statement}.
	 * @param ctx the parse tree
	 */
	void enterStatement(GolampiParser.StatementContext ctx);
	/**
	 * Exit a parse tree produced by {@link GolampiParser#statement}.
	 * @param ctx the parse tree
	 */
	void exitStatement(GolampiParser.StatementContext ctx);
	/**
	 * Enter a parse tree produced by the {@code SimpleAssignment}
	 * labeled alternative in {@link GolampiParser#assignment}.
	 * @param ctx the parse tree
	 */
	void enterSimpleAssignment(GolampiParser.SimpleAssignmentContext ctx);
	/**
	 * Exit a parse tree produced by the {@code SimpleAssignment}
	 * labeled alternative in {@link GolampiParser#assignment}.
	 * @param ctx the parse tree
	 */
	void exitSimpleAssignment(GolampiParser.SimpleAssignmentContext ctx);
	/**
	 * Enter a parse tree produced by the {@code ArrayAssignment}
	 * labeled alternative in {@link GolampiParser#assignment}.
	 * @param ctx the parse tree
	 */
	void enterArrayAssignment(GolampiParser.ArrayAssignmentContext ctx);
	/**
	 * Exit a parse tree produced by the {@code ArrayAssignment}
	 * labeled alternative in {@link GolampiParser#assignment}.
	 * @param ctx the parse tree
	 */
	void exitArrayAssignment(GolampiParser.ArrayAssignmentContext ctx);
	/**
	 * Enter a parse tree produced by the {@code PointerAssignment}
	 * labeled alternative in {@link GolampiParser#assignment}.
	 * @param ctx the parse tree
	 */
	void enterPointerAssignment(GolampiParser.PointerAssignmentContext ctx);
	/**
	 * Exit a parse tree produced by the {@code PointerAssignment}
	 * labeled alternative in {@link GolampiParser#assignment}.
	 * @param ctx the parse tree
	 */
	void exitPointerAssignment(GolampiParser.PointerAssignmentContext ctx);
	/**
	 * Enter a parse tree produced by {@link GolampiParser#assignOp}.
	 * @param ctx the parse tree
	 */
	void enterAssignOp(GolampiParser.AssignOpContext ctx);
	/**
	 * Exit a parse tree produced by {@link GolampiParser#assignOp}.
	 * @param ctx the parse tree
	 */
	void exitAssignOp(GolampiParser.AssignOpContext ctx);
	/**
	 * Enter a parse tree produced by the {@code IncrementStatement}
	 * labeled alternative in {@link GolampiParser#incDecStatement}.
	 * @param ctx the parse tree
	 */
	void enterIncrementStatement(GolampiParser.IncrementStatementContext ctx);
	/**
	 * Exit a parse tree produced by the {@code IncrementStatement}
	 * labeled alternative in {@link GolampiParser#incDecStatement}.
	 * @param ctx the parse tree
	 */
	void exitIncrementStatement(GolampiParser.IncrementStatementContext ctx);
	/**
	 * Enter a parse tree produced by the {@code DecrementStatement}
	 * labeled alternative in {@link GolampiParser#incDecStatement}.
	 * @param ctx the parse tree
	 */
	void enterDecrementStatement(GolampiParser.DecrementStatementContext ctx);
	/**
	 * Exit a parse tree produced by the {@code DecrementStatement}
	 * labeled alternative in {@link GolampiParser#incDecStatement}.
	 * @param ctx the parse tree
	 */
	void exitDecrementStatement(GolampiParser.DecrementStatementContext ctx);
	/**
	 * Enter a parse tree produced by the {@code IfElseIfElse}
	 * labeled alternative in {@link GolampiParser#ifStatement}.
	 * @param ctx the parse tree
	 */
	void enterIfElseIfElse(GolampiParser.IfElseIfElseContext ctx);
	/**
	 * Exit a parse tree produced by the {@code IfElseIfElse}
	 * labeled alternative in {@link GolampiParser#ifStatement}.
	 * @param ctx the parse tree
	 */
	void exitIfElseIfElse(GolampiParser.IfElseIfElseContext ctx);
	/**
	 * Enter a parse tree produced by {@link GolampiParser#switchStatement}.
	 * @param ctx the parse tree
	 */
	void enterSwitchStatement(GolampiParser.SwitchStatementContext ctx);
	/**
	 * Exit a parse tree produced by {@link GolampiParser#switchStatement}.
	 * @param ctx the parse tree
	 */
	void exitSwitchStatement(GolampiParser.SwitchStatementContext ctx);
	/**
	 * Enter a parse tree produced by {@link GolampiParser#caseClause}.
	 * @param ctx the parse tree
	 */
	void enterCaseClause(GolampiParser.CaseClauseContext ctx);
	/**
	 * Exit a parse tree produced by {@link GolampiParser#caseClause}.
	 * @param ctx the parse tree
	 */
	void exitCaseClause(GolampiParser.CaseClauseContext ctx);
	/**
	 * Enter a parse tree produced by {@link GolampiParser#defaultClause}.
	 * @param ctx the parse tree
	 */
	void enterDefaultClause(GolampiParser.DefaultClauseContext ctx);
	/**
	 * Exit a parse tree produced by {@link GolampiParser#defaultClause}.
	 * @param ctx the parse tree
	 */
	void exitDefaultClause(GolampiParser.DefaultClauseContext ctx);
	/**
	 * Enter a parse tree produced by the {@code ForTraditional}
	 * labeled alternative in {@link GolampiParser#forStatement}.
	 * @param ctx the parse tree
	 */
	void enterForTraditional(GolampiParser.ForTraditionalContext ctx);
	/**
	 * Exit a parse tree produced by the {@code ForTraditional}
	 * labeled alternative in {@link GolampiParser#forStatement}.
	 * @param ctx the parse tree
	 */
	void exitForTraditional(GolampiParser.ForTraditionalContext ctx);
	/**
	 * Enter a parse tree produced by the {@code ForWhile}
	 * labeled alternative in {@link GolampiParser#forStatement}.
	 * @param ctx the parse tree
	 */
	void enterForWhile(GolampiParser.ForWhileContext ctx);
	/**
	 * Exit a parse tree produced by the {@code ForWhile}
	 * labeled alternative in {@link GolampiParser#forStatement}.
	 * @param ctx the parse tree
	 */
	void exitForWhile(GolampiParser.ForWhileContext ctx);
	/**
	 * Enter a parse tree produced by the {@code ForInfinite}
	 * labeled alternative in {@link GolampiParser#forStatement}.
	 * @param ctx the parse tree
	 */
	void enterForInfinite(GolampiParser.ForInfiniteContext ctx);
	/**
	 * Exit a parse tree produced by the {@code ForInfinite}
	 * labeled alternative in {@link GolampiParser#forStatement}.
	 * @param ctx the parse tree
	 */
	void exitForInfinite(GolampiParser.ForInfiniteContext ctx);
	/**
	 * Enter a parse tree produced by {@link GolampiParser#forClause}.
	 * @param ctx the parse tree
	 */
	void enterForClause(GolampiParser.ForClauseContext ctx);
	/**
	 * Exit a parse tree produced by {@link GolampiParser#forClause}.
	 * @param ctx the parse tree
	 */
	void exitForClause(GolampiParser.ForClauseContext ctx);
	/**
	 * Enter a parse tree produced by {@link GolampiParser#forInit}.
	 * @param ctx the parse tree
	 */
	void enterForInit(GolampiParser.ForInitContext ctx);
	/**
	 * Exit a parse tree produced by {@link GolampiParser#forInit}.
	 * @param ctx the parse tree
	 */
	void exitForInit(GolampiParser.ForInitContext ctx);
	/**
	 * Enter a parse tree produced by {@link GolampiParser#forPost}.
	 * @param ctx the parse tree
	 */
	void enterForPost(GolampiParser.ForPostContext ctx);
	/**
	 * Exit a parse tree produced by {@link GolampiParser#forPost}.
	 * @param ctx the parse tree
	 */
	void exitForPost(GolampiParser.ForPostContext ctx);
	/**
	 * Enter a parse tree produced by {@link GolampiParser#breakStatement}.
	 * @param ctx the parse tree
	 */
	void enterBreakStatement(GolampiParser.BreakStatementContext ctx);
	/**
	 * Exit a parse tree produced by {@link GolampiParser#breakStatement}.
	 * @param ctx the parse tree
	 */
	void exitBreakStatement(GolampiParser.BreakStatementContext ctx);
	/**
	 * Enter a parse tree produced by {@link GolampiParser#continueStatement}.
	 * @param ctx the parse tree
	 */
	void enterContinueStatement(GolampiParser.ContinueStatementContext ctx);
	/**
	 * Exit a parse tree produced by {@link GolampiParser#continueStatement}.
	 * @param ctx the parse tree
	 */
	void exitContinueStatement(GolampiParser.ContinueStatementContext ctx);
	/**
	 * Enter a parse tree produced by {@link GolampiParser#returnStatement}.
	 * @param ctx the parse tree
	 */
	void enterReturnStatement(GolampiParser.ReturnStatementContext ctx);
	/**
	 * Exit a parse tree produced by {@link GolampiParser#returnStatement}.
	 * @param ctx the parse tree
	 */
	void exitReturnStatement(GolampiParser.ReturnStatementContext ctx);
	/**
	 * Enter a parse tree produced by {@link GolampiParser#block}.
	 * @param ctx the parse tree
	 */
	void enterBlock(GolampiParser.BlockContext ctx);
	/**
	 * Exit a parse tree produced by {@link GolampiParser#block}.
	 * @param ctx the parse tree
	 */
	void exitBlock(GolampiParser.BlockContext ctx);
	/**
	 * Enter a parse tree produced by {@link GolampiParser#expressionStatement}.
	 * @param ctx the parse tree
	 */
	void enterExpressionStatement(GolampiParser.ExpressionStatementContext ctx);
	/**
	 * Exit a parse tree produced by {@link GolampiParser#expressionStatement}.
	 * @param ctx the parse tree
	 */
	void exitExpressionStatement(GolampiParser.ExpressionStatementContext ctx);
	/**
	 * Enter a parse tree produced by {@link GolampiParser#expression}.
	 * @param ctx the parse tree
	 */
	void enterExpression(GolampiParser.ExpressionContext ctx);
	/**
	 * Exit a parse tree produced by {@link GolampiParser#expression}.
	 * @param ctx the parse tree
	 */
	void exitExpression(GolampiParser.ExpressionContext ctx);
	/**
	 * Enter a parse tree produced by {@link GolampiParser#logicalOr}.
	 * @param ctx the parse tree
	 */
	void enterLogicalOr(GolampiParser.LogicalOrContext ctx);
	/**
	 * Exit a parse tree produced by {@link GolampiParser#logicalOr}.
	 * @param ctx the parse tree
	 */
	void exitLogicalOr(GolampiParser.LogicalOrContext ctx);
	/**
	 * Enter a parse tree produced by {@link GolampiParser#logicalAnd}.
	 * @param ctx the parse tree
	 */
	void enterLogicalAnd(GolampiParser.LogicalAndContext ctx);
	/**
	 * Exit a parse tree produced by {@link GolampiParser#logicalAnd}.
	 * @param ctx the parse tree
	 */
	void exitLogicalAnd(GolampiParser.LogicalAndContext ctx);
	/**
	 * Enter a parse tree produced by {@link GolampiParser#equality}.
	 * @param ctx the parse tree
	 */
	void enterEquality(GolampiParser.EqualityContext ctx);
	/**
	 * Exit a parse tree produced by {@link GolampiParser#equality}.
	 * @param ctx the parse tree
	 */
	void exitEquality(GolampiParser.EqualityContext ctx);
	/**
	 * Enter a parse tree produced by {@link GolampiParser#relational}.
	 * @param ctx the parse tree
	 */
	void enterRelational(GolampiParser.RelationalContext ctx);
	/**
	 * Exit a parse tree produced by {@link GolampiParser#relational}.
	 * @param ctx the parse tree
	 */
	void exitRelational(GolampiParser.RelationalContext ctx);
	/**
	 * Enter a parse tree produced by {@link GolampiParser#additive}.
	 * @param ctx the parse tree
	 */
	void enterAdditive(GolampiParser.AdditiveContext ctx);
	/**
	 * Exit a parse tree produced by {@link GolampiParser#additive}.
	 * @param ctx the parse tree
	 */
	void exitAdditive(GolampiParser.AdditiveContext ctx);
	/**
	 * Enter a parse tree produced by {@link GolampiParser#multiplicative}.
	 * @param ctx the parse tree
	 */
	void enterMultiplicative(GolampiParser.MultiplicativeContext ctx);
	/**
	 * Exit a parse tree produced by {@link GolampiParser#multiplicative}.
	 * @param ctx the parse tree
	 */
	void exitMultiplicative(GolampiParser.MultiplicativeContext ctx);
	/**
	 * Enter a parse tree produced by the {@code PrimaryUnary}
	 * labeled alternative in {@link GolampiParser#unary}.
	 * @param ctx the parse tree
	 */
	void enterPrimaryUnary(GolampiParser.PrimaryUnaryContext ctx);
	/**
	 * Exit a parse tree produced by the {@code PrimaryUnary}
	 * labeled alternative in {@link GolampiParser#unary}.
	 * @param ctx the parse tree
	 */
	void exitPrimaryUnary(GolampiParser.PrimaryUnaryContext ctx);
	/**
	 * Enter a parse tree produced by the {@code NegativeUnary}
	 * labeled alternative in {@link GolampiParser#unary}.
	 * @param ctx the parse tree
	 */
	void enterNegativeUnary(GolampiParser.NegativeUnaryContext ctx);
	/**
	 * Exit a parse tree produced by the {@code NegativeUnary}
	 * labeled alternative in {@link GolampiParser#unary}.
	 * @param ctx the parse tree
	 */
	void exitNegativeUnary(GolampiParser.NegativeUnaryContext ctx);
	/**
	 * Enter a parse tree produced by the {@code NotUnary}
	 * labeled alternative in {@link GolampiParser#unary}.
	 * @param ctx the parse tree
	 */
	void enterNotUnary(GolampiParser.NotUnaryContext ctx);
	/**
	 * Exit a parse tree produced by the {@code NotUnary}
	 * labeled alternative in {@link GolampiParser#unary}.
	 * @param ctx the parse tree
	 */
	void exitNotUnary(GolampiParser.NotUnaryContext ctx);
	/**
	 * Enter a parse tree produced by the {@code AddressOf}
	 * labeled alternative in {@link GolampiParser#unary}.
	 * @param ctx the parse tree
	 */
	void enterAddressOf(GolampiParser.AddressOfContext ctx);
	/**
	 * Exit a parse tree produced by the {@code AddressOf}
	 * labeled alternative in {@link GolampiParser#unary}.
	 * @param ctx the parse tree
	 */
	void exitAddressOf(GolampiParser.AddressOfContext ctx);
	/**
	 * Enter a parse tree produced by the {@code Dereference}
	 * labeled alternative in {@link GolampiParser#unary}.
	 * @param ctx the parse tree
	 */
	void enterDereference(GolampiParser.DereferenceContext ctx);
	/**
	 * Exit a parse tree produced by the {@code Dereference}
	 * labeled alternative in {@link GolampiParser#unary}.
	 * @param ctx the parse tree
	 */
	void exitDereference(GolampiParser.DereferenceContext ctx);
	/**
	 * Enter a parse tree produced by the {@code IntLiteral}
	 * labeled alternative in {@link GolampiParser#primary}.
	 * @param ctx the parse tree
	 */
	void enterIntLiteral(GolampiParser.IntLiteralContext ctx);
	/**
	 * Exit a parse tree produced by the {@code IntLiteral}
	 * labeled alternative in {@link GolampiParser#primary}.
	 * @param ctx the parse tree
	 */
	void exitIntLiteral(GolampiParser.IntLiteralContext ctx);
	/**
	 * Enter a parse tree produced by the {@code FloatLiteral}
	 * labeled alternative in {@link GolampiParser#primary}.
	 * @param ctx the parse tree
	 */
	void enterFloatLiteral(GolampiParser.FloatLiteralContext ctx);
	/**
	 * Exit a parse tree produced by the {@code FloatLiteral}
	 * labeled alternative in {@link GolampiParser#primary}.
	 * @param ctx the parse tree
	 */
	void exitFloatLiteral(GolampiParser.FloatLiteralContext ctx);
	/**
	 * Enter a parse tree produced by the {@code RuneLiteral}
	 * labeled alternative in {@link GolampiParser#primary}.
	 * @param ctx the parse tree
	 */
	void enterRuneLiteral(GolampiParser.RuneLiteralContext ctx);
	/**
	 * Exit a parse tree produced by the {@code RuneLiteral}
	 * labeled alternative in {@link GolampiParser#primary}.
	 * @param ctx the parse tree
	 */
	void exitRuneLiteral(GolampiParser.RuneLiteralContext ctx);
	/**
	 * Enter a parse tree produced by the {@code StringLiteral}
	 * labeled alternative in {@link GolampiParser#primary}.
	 * @param ctx the parse tree
	 */
	void enterStringLiteral(GolampiParser.StringLiteralContext ctx);
	/**
	 * Exit a parse tree produced by the {@code StringLiteral}
	 * labeled alternative in {@link GolampiParser#primary}.
	 * @param ctx the parse tree
	 */
	void exitStringLiteral(GolampiParser.StringLiteralContext ctx);
	/**
	 * Enter a parse tree produced by the {@code TrueLiteral}
	 * labeled alternative in {@link GolampiParser#primary}.
	 * @param ctx the parse tree
	 */
	void enterTrueLiteral(GolampiParser.TrueLiteralContext ctx);
	/**
	 * Exit a parse tree produced by the {@code TrueLiteral}
	 * labeled alternative in {@link GolampiParser#primary}.
	 * @param ctx the parse tree
	 */
	void exitTrueLiteral(GolampiParser.TrueLiteralContext ctx);
	/**
	 * Enter a parse tree produced by the {@code FalseLiteral}
	 * labeled alternative in {@link GolampiParser#primary}.
	 * @param ctx the parse tree
	 */
	void enterFalseLiteral(GolampiParser.FalseLiteralContext ctx);
	/**
	 * Exit a parse tree produced by the {@code FalseLiteral}
	 * labeled alternative in {@link GolampiParser#primary}.
	 * @param ctx the parse tree
	 */
	void exitFalseLiteral(GolampiParser.FalseLiteralContext ctx);
	/**
	 * Enter a parse tree produced by the {@code NilLiteral}
	 * labeled alternative in {@link GolampiParser#primary}.
	 * @param ctx the parse tree
	 */
	void enterNilLiteral(GolampiParser.NilLiteralContext ctx);
	/**
	 * Exit a parse tree produced by the {@code NilLiteral}
	 * labeled alternative in {@link GolampiParser#primary}.
	 * @param ctx the parse tree
	 */
	void exitNilLiteral(GolampiParser.NilLiteralContext ctx);
	/**
	 * Enter a parse tree produced by the {@code FunctionCall}
	 * labeled alternative in {@link GolampiParser#primary}.
	 * @param ctx the parse tree
	 */
	void enterFunctionCall(GolampiParser.FunctionCallContext ctx);
	/**
	 * Exit a parse tree produced by the {@code FunctionCall}
	 * labeled alternative in {@link GolampiParser#primary}.
	 * @param ctx the parse tree
	 */
	void exitFunctionCall(GolampiParser.FunctionCallContext ctx);
	/**
	 * Enter a parse tree produced by the {@code ArrayAccess}
	 * labeled alternative in {@link GolampiParser#primary}.
	 * @param ctx the parse tree
	 */
	void enterArrayAccess(GolampiParser.ArrayAccessContext ctx);
	/**
	 * Exit a parse tree produced by the {@code ArrayAccess}
	 * labeled alternative in {@link GolampiParser#primary}.
	 * @param ctx the parse tree
	 */
	void exitArrayAccess(GolampiParser.ArrayAccessContext ctx);
	/**
	 * Enter a parse tree produced by the {@code Identifier}
	 * labeled alternative in {@link GolampiParser#primary}.
	 * @param ctx the parse tree
	 */
	void enterIdentifier(GolampiParser.IdentifierContext ctx);
	/**
	 * Exit a parse tree produced by the {@code Identifier}
	 * labeled alternative in {@link GolampiParser#primary}.
	 * @param ctx the parse tree
	 */
	void exitIdentifier(GolampiParser.IdentifierContext ctx);
	/**
	 * Enter a parse tree produced by the {@code GroupedExpression}
	 * labeled alternative in {@link GolampiParser#primary}.
	 * @param ctx the parse tree
	 */
	void enterGroupedExpression(GolampiParser.GroupedExpressionContext ctx);
	/**
	 * Exit a parse tree produced by the {@code GroupedExpression}
	 * labeled alternative in {@link GolampiParser#primary}.
	 * @param ctx the parse tree
	 */
	void exitGroupedExpression(GolampiParser.GroupedExpressionContext ctx);
	/**
	 * Enter a parse tree produced by the {@code ArrayLiteralExpr}
	 * labeled alternative in {@link GolampiParser#primary}.
	 * @param ctx the parse tree
	 */
	void enterArrayLiteralExpr(GolampiParser.ArrayLiteralExprContext ctx);
	/**
	 * Exit a parse tree produced by the {@code ArrayLiteralExpr}
	 * labeled alternative in {@link GolampiParser#primary}.
	 * @param ctx the parse tree
	 */
	void exitArrayLiteralExpr(GolampiParser.ArrayLiteralExprContext ctx);
	/**
	 * Enter a parse tree produced by the {@code InnerArrayLiteral}
	 * labeled alternative in {@link GolampiParser#primary}.
	 * @param ctx the parse tree
	 */
	void enterInnerArrayLiteral(GolampiParser.InnerArrayLiteralContext ctx);
	/**
	 * Exit a parse tree produced by the {@code InnerArrayLiteral}
	 * labeled alternative in {@link GolampiParser#primary}.
	 * @param ctx the parse tree
	 */
	void exitInnerArrayLiteral(GolampiParser.InnerArrayLiteralContext ctx);
	/**
	 * Enter a parse tree produced by the {@code FixedArrayLiteralNode}
	 * labeled alternative in {@link GolampiParser#arrayLiteral}.
	 * @param ctx the parse tree
	 */
	void enterFixedArrayLiteralNode(GolampiParser.FixedArrayLiteralNodeContext ctx);
	/**
	 * Exit a parse tree produced by the {@code FixedArrayLiteralNode}
	 * labeled alternative in {@link GolampiParser#arrayLiteral}.
	 * @param ctx the parse tree
	 */
	void exitFixedArrayLiteralNode(GolampiParser.FixedArrayLiteralNodeContext ctx);
	/**
	 * Enter a parse tree produced by the {@code SliceLiteralNode}
	 * labeled alternative in {@link GolampiParser#arrayLiteral}.
	 * @param ctx the parse tree
	 */
	void enterSliceLiteralNode(GolampiParser.SliceLiteralNodeContext ctx);
	/**
	 * Exit a parse tree produced by the {@code SliceLiteralNode}
	 * labeled alternative in {@link GolampiParser#arrayLiteral}.
	 * @param ctx the parse tree
	 */
	void exitSliceLiteralNode(GolampiParser.SliceLiteralNodeContext ctx);
	/**
	 * Enter a parse tree produced by {@link GolampiParser#innerLiteralList}.
	 * @param ctx the parse tree
	 */
	void enterInnerLiteralList(GolampiParser.InnerLiteralListContext ctx);
	/**
	 * Exit a parse tree produced by {@link GolampiParser#innerLiteralList}.
	 * @param ctx the parse tree
	 */
	void exitInnerLiteralList(GolampiParser.InnerLiteralListContext ctx);
	/**
	 * Enter a parse tree produced by {@link GolampiParser#innerLiteral}.
	 * @param ctx the parse tree
	 */
	void enterInnerLiteral(GolampiParser.InnerLiteralContext ctx);
	/**
	 * Exit a parse tree produced by {@link GolampiParser#innerLiteral}.
	 * @param ctx the parse tree
	 */
	void exitInnerLiteral(GolampiParser.InnerLiteralContext ctx);
	/**
	 * Enter a parse tree produced by {@link GolampiParser#argumentList}.
	 * @param ctx the parse tree
	 */
	void enterArgumentList(GolampiParser.ArgumentListContext ctx);
	/**
	 * Exit a parse tree produced by {@link GolampiParser#argumentList}.
	 * @param ctx the parse tree
	 */
	void exitArgumentList(GolampiParser.ArgumentListContext ctx);
	/**
	 * Enter a parse tree produced by the {@code ExpressionArgument}
	 * labeled alternative in {@link GolampiParser#argument}.
	 * @param ctx the parse tree
	 */
	void enterExpressionArgument(GolampiParser.ExpressionArgumentContext ctx);
	/**
	 * Exit a parse tree produced by the {@code ExpressionArgument}
	 * labeled alternative in {@link GolampiParser#argument}.
	 * @param ctx the parse tree
	 */
	void exitExpressionArgument(GolampiParser.ExpressionArgumentContext ctx);
	/**
	 * Enter a parse tree produced by the {@code AddressArgument}
	 * labeled alternative in {@link GolampiParser#argument}.
	 * @param ctx the parse tree
	 */
	void enterAddressArgument(GolampiParser.AddressArgumentContext ctx);
	/**
	 * Exit a parse tree produced by the {@code AddressArgument}
	 * labeled alternative in {@link GolampiParser#argument}.
	 * @param ctx the parse tree
	 */
	void exitAddressArgument(GolampiParser.AddressArgumentContext ctx);
	/**
	 * Enter a parse tree produced by the {@code Int32Type}
	 * labeled alternative in {@link GolampiParser#type}.
	 * @param ctx the parse tree
	 */
	void enterInt32Type(GolampiParser.Int32TypeContext ctx);
	/**
	 * Exit a parse tree produced by the {@code Int32Type}
	 * labeled alternative in {@link GolampiParser#type}.
	 * @param ctx the parse tree
	 */
	void exitInt32Type(GolampiParser.Int32TypeContext ctx);
	/**
	 * Enter a parse tree produced by the {@code Float32Type}
	 * labeled alternative in {@link GolampiParser#type}.
	 * @param ctx the parse tree
	 */
	void enterFloat32Type(GolampiParser.Float32TypeContext ctx);
	/**
	 * Exit a parse tree produced by the {@code Float32Type}
	 * labeled alternative in {@link GolampiParser#type}.
	 * @param ctx the parse tree
	 */
	void exitFloat32Type(GolampiParser.Float32TypeContext ctx);
	/**
	 * Enter a parse tree produced by the {@code BoolType}
	 * labeled alternative in {@link GolampiParser#type}.
	 * @param ctx the parse tree
	 */
	void enterBoolType(GolampiParser.BoolTypeContext ctx);
	/**
	 * Exit a parse tree produced by the {@code BoolType}
	 * labeled alternative in {@link GolampiParser#type}.
	 * @param ctx the parse tree
	 */
	void exitBoolType(GolampiParser.BoolTypeContext ctx);
	/**
	 * Enter a parse tree produced by the {@code RuneType}
	 * labeled alternative in {@link GolampiParser#type}.
	 * @param ctx the parse tree
	 */
	void enterRuneType(GolampiParser.RuneTypeContext ctx);
	/**
	 * Exit a parse tree produced by the {@code RuneType}
	 * labeled alternative in {@link GolampiParser#type}.
	 * @param ctx the parse tree
	 */
	void exitRuneType(GolampiParser.RuneTypeContext ctx);
	/**
	 * Enter a parse tree produced by the {@code StringType}
	 * labeled alternative in {@link GolampiParser#type}.
	 * @param ctx the parse tree
	 */
	void enterStringType(GolampiParser.StringTypeContext ctx);
	/**
	 * Exit a parse tree produced by the {@code StringType}
	 * labeled alternative in {@link GolampiParser#type}.
	 * @param ctx the parse tree
	 */
	void exitStringType(GolampiParser.StringTypeContext ctx);
	/**
	 * Enter a parse tree produced by the {@code ArrayType}
	 * labeled alternative in {@link GolampiParser#type}.
	 * @param ctx the parse tree
	 */
	void enterArrayType(GolampiParser.ArrayTypeContext ctx);
	/**
	 * Exit a parse tree produced by the {@code ArrayType}
	 * labeled alternative in {@link GolampiParser#type}.
	 * @param ctx the parse tree
	 */
	void exitArrayType(GolampiParser.ArrayTypeContext ctx);
	/**
	 * Enter a parse tree produced by the {@code SliceType}
	 * labeled alternative in {@link GolampiParser#type}.
	 * @param ctx the parse tree
	 */
	void enterSliceType(GolampiParser.SliceTypeContext ctx);
	/**
	 * Exit a parse tree produced by the {@code SliceType}
	 * labeled alternative in {@link GolampiParser#type}.
	 * @param ctx the parse tree
	 */
	void exitSliceType(GolampiParser.SliceTypeContext ctx);
	/**
	 * Enter a parse tree produced by the {@code PointerType}
	 * labeled alternative in {@link GolampiParser#type}.
	 * @param ctx the parse tree
	 */
	void enterPointerType(GolampiParser.PointerTypeContext ctx);
	/**
	 * Exit a parse tree produced by the {@code PointerType}
	 * labeled alternative in {@link GolampiParser#type}.
	 * @param ctx the parse tree
	 */
	void exitPointerType(GolampiParser.PointerTypeContext ctx);
}