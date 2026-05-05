<?php

/*
 * Generated from Golampi.g4 by ANTLR 4.13.0
 */

use Antlr\Antlr4\Runtime\Tree\ParseTreeVisitor;

/**
 * This interface defines a complete generic visitor for a parse tree produced by {@see GolampiParser}.
 */
interface GolampiVisitor extends ParseTreeVisitor
{
	/**
	 * Visit a parse tree produced by {@see GolampiParser::program()}.
	 *
	 * @param Context\ProgramContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitProgram(Context\ProgramContext $context);

	/**
	 * Visit a parse tree produced by {@see GolampiParser::declaration()}.
	 *
	 * @param Context\DeclarationContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitDeclaration(Context\DeclarationContext $context);

	/**
	 * Visit a parse tree produced by the `VarDeclSimple` labeled alternative
	 * in {@see GolampiParser::varDeclaration()}.
	 *
	 * @param Context\VarDeclSimpleContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitVarDeclSimple(Context\VarDeclSimpleContext $context);

	/**
	 * Visit a parse tree produced by the `VarDeclWithInit` labeled alternative
	 * in {@see GolampiParser::varDeclaration()}.
	 *
	 * @param Context\VarDeclWithInitContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitVarDeclWithInit(Context\VarDeclWithInitContext $context);

	/**
	 * Visit a parse tree produced by the `ShortVarDecl` labeled alternative
	 * in {@see GolampiParser::shortVarDeclaration()}.
	 *
	 * @param Context\ShortVarDeclContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitShortVarDecl(Context\ShortVarDeclContext $context);

	/**
	 * Visit a parse tree produced by the `ConstDecl` labeled alternative
	 * in {@see GolampiParser::constDeclaration()}.
	 *
	 * @param Context\ConstDeclContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitConstDecl(Context\ConstDeclContext $context);

	/**
	 * Visit a parse tree produced by the `FuncDeclSingleReturn` labeled alternative
	 * in {@see GolampiParser::functionDeclaration()}.
	 *
	 * @param Context\FuncDeclSingleReturnContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitFuncDeclSingleReturn(Context\FuncDeclSingleReturnContext $context);

	/**
	 * Visit a parse tree produced by the `FuncDeclMultiReturn` labeled alternative
	 * in {@see GolampiParser::functionDeclaration()}.
	 *
	 * @param Context\FuncDeclMultiReturnContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitFuncDeclMultiReturn(Context\FuncDeclMultiReturnContext $context);

	/**
	 * Visit a parse tree produced by {@see GolampiParser::parameterList()}.
	 *
	 * @param Context\ParameterListContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitParameterList(Context\ParameterListContext $context);

	/**
	 * Visit a parse tree produced by the `NormalParameter` labeled alternative
	 * in {@see GolampiParser::parameter()}.
	 *
	 * @param Context\NormalParameterContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitNormalParameter(Context\NormalParameterContext $context);

	/**
	 * Visit a parse tree produced by the `PointerParameter` labeled alternative
	 * in {@see GolampiParser::parameter()}.
	 *
	 * @param Context\PointerParameterContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitPointerParameter(Context\PointerParameterContext $context);

	/**
	 * Visit a parse tree produced by {@see GolampiParser::typeList()}.
	 *
	 * @param Context\TypeListContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitTypeList(Context\TypeListContext $context);

	/**
	 * Visit a parse tree produced by {@see GolampiParser::idList()}.
	 *
	 * @param Context\IdListContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitIdList(Context\IdListContext $context);

	/**
	 * Visit a parse tree produced by {@see GolampiParser::expressionList()}.
	 *
	 * @param Context\ExpressionListContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitExpressionList(Context\ExpressionListContext $context);

	/**
	 * Visit a parse tree produced by {@see GolampiParser::statement()}.
	 *
	 * @param Context\StatementContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitStatement(Context\StatementContext $context);

	/**
	 * Visit a parse tree produced by the `SimpleAssignment` labeled alternative
	 * in {@see GolampiParser::assignment()}.
	 *
	 * @param Context\SimpleAssignmentContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitSimpleAssignment(Context\SimpleAssignmentContext $context);

	/**
	 * Visit a parse tree produced by the `ArrayAssignment` labeled alternative
	 * in {@see GolampiParser::assignment()}.
	 *
	 * @param Context\ArrayAssignmentContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitArrayAssignment(Context\ArrayAssignmentContext $context);

	/**
	 * Visit a parse tree produced by the `PointerAssignment` labeled alternative
	 * in {@see GolampiParser::assignment()}.
	 *
	 * @param Context\PointerAssignmentContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitPointerAssignment(Context\PointerAssignmentContext $context);

	/**
	 * Visit a parse tree produced by {@see GolampiParser::assignOp()}.
	 *
	 * @param Context\AssignOpContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitAssignOp(Context\AssignOpContext $context);

	/**
	 * Visit a parse tree produced by the `IncrementStatement` labeled alternative
	 * in {@see GolampiParser::incDecStatement()}.
	 *
	 * @param Context\IncrementStatementContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitIncrementStatement(Context\IncrementStatementContext $context);

	/**
	 * Visit a parse tree produced by the `DecrementStatement` labeled alternative
	 * in {@see GolampiParser::incDecStatement()}.
	 *
	 * @param Context\DecrementStatementContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitDecrementStatement(Context\DecrementStatementContext $context);

	/**
	 * Visit a parse tree produced by the `IfElseIfElse` labeled alternative
	 * in {@see GolampiParser::ifStatement()}.
	 *
	 * @param Context\IfElseIfElseContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitIfElseIfElse(Context\IfElseIfElseContext $context);

	/**
	 * Visit a parse tree produced by {@see GolampiParser::switchStatement()}.
	 *
	 * @param Context\SwitchStatementContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitSwitchStatement(Context\SwitchStatementContext $context);

	/**
	 * Visit a parse tree produced by {@see GolampiParser::caseClause()}.
	 *
	 * @param Context\CaseClauseContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitCaseClause(Context\CaseClauseContext $context);

	/**
	 * Visit a parse tree produced by {@see GolampiParser::defaultClause()}.
	 *
	 * @param Context\DefaultClauseContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitDefaultClause(Context\DefaultClauseContext $context);

	/**
	 * Visit a parse tree produced by the `ForTraditional` labeled alternative
	 * in {@see GolampiParser::forStatement()}.
	 *
	 * @param Context\ForTraditionalContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitForTraditional(Context\ForTraditionalContext $context);

	/**
	 * Visit a parse tree produced by the `ForWhile` labeled alternative
	 * in {@see GolampiParser::forStatement()}.
	 *
	 * @param Context\ForWhileContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitForWhile(Context\ForWhileContext $context);

	/**
	 * Visit a parse tree produced by the `ForInfinite` labeled alternative
	 * in {@see GolampiParser::forStatement()}.
	 *
	 * @param Context\ForInfiniteContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitForInfinite(Context\ForInfiniteContext $context);

	/**
	 * Visit a parse tree produced by {@see GolampiParser::forClause()}.
	 *
	 * @param Context\ForClauseContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitForClause(Context\ForClauseContext $context);

	/**
	 * Visit a parse tree produced by {@see GolampiParser::forInit()}.
	 *
	 * @param Context\ForInitContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitForInit(Context\ForInitContext $context);

	/**
	 * Visit a parse tree produced by {@see GolampiParser::forPost()}.
	 *
	 * @param Context\ForPostContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitForPost(Context\ForPostContext $context);

	/**
	 * Visit a parse tree produced by {@see GolampiParser::breakStatement()}.
	 *
	 * @param Context\BreakStatementContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitBreakStatement(Context\BreakStatementContext $context);

	/**
	 * Visit a parse tree produced by {@see GolampiParser::continueStatement()}.
	 *
	 * @param Context\ContinueStatementContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitContinueStatement(Context\ContinueStatementContext $context);

	/**
	 * Visit a parse tree produced by {@see GolampiParser::returnStatement()}.
	 *
	 * @param Context\ReturnStatementContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitReturnStatement(Context\ReturnStatementContext $context);

	/**
	 * Visit a parse tree produced by {@see GolampiParser::block()}.
	 *
	 * @param Context\BlockContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitBlock(Context\BlockContext $context);

	/**
	 * Visit a parse tree produced by {@see GolampiParser::expressionStatement()}.
	 *
	 * @param Context\ExpressionStatementContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitExpressionStatement(Context\ExpressionStatementContext $context);

	/**
	 * Visit a parse tree produced by the `TernaryExpr` labeled alternative
	 * in {@see GolampiParser::expression()}.
	 *
	 * @param Context\TernaryExprContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitTernaryExpr(Context\TernaryExprContext $context);

	/**
	 * Visit a parse tree produced by the `NonTernaryExpr` labeled alternative
	 * in {@see GolampiParser::expression()}.
	 *
	 * @param Context\NonTernaryExprContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitNonTernaryExpr(Context\NonTernaryExprContext $context);

	/**
	 * Visit a parse tree produced by {@see GolampiParser::logicalOr()}.
	 *
	 * @param Context\LogicalOrContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitLogicalOr(Context\LogicalOrContext $context);

	/**
	 * Visit a parse tree produced by {@see GolampiParser::logicalAnd()}.
	 *
	 * @param Context\LogicalAndContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitLogicalAnd(Context\LogicalAndContext $context);

	/**
	 * Visit a parse tree produced by {@see GolampiParser::equality()}.
	 *
	 * @param Context\EqualityContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitEquality(Context\EqualityContext $context);

	/**
	 * Visit a parse tree produced by {@see GolampiParser::relational()}.
	 *
	 * @param Context\RelationalContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitRelational(Context\RelationalContext $context);

	/**
	 * Visit a parse tree produced by {@see GolampiParser::additive()}.
	 *
	 * @param Context\AdditiveContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitAdditive(Context\AdditiveContext $context);

	/**
	 * Visit a parse tree produced by {@see GolampiParser::multiplicative()}.
	 *
	 * @param Context\MultiplicativeContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitMultiplicative(Context\MultiplicativeContext $context);

	/**
	 * Visit a parse tree produced by the `PrimaryUnary` labeled alternative
	 * in {@see GolampiParser::unary()}.
	 *
	 * @param Context\PrimaryUnaryContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitPrimaryUnary(Context\PrimaryUnaryContext $context);

	/**
	 * Visit a parse tree produced by the `NegativeUnary` labeled alternative
	 * in {@see GolampiParser::unary()}.
	 *
	 * @param Context\NegativeUnaryContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitNegativeUnary(Context\NegativeUnaryContext $context);

	/**
	 * Visit a parse tree produced by the `NotUnary` labeled alternative
	 * in {@see GolampiParser::unary()}.
	 *
	 * @param Context\NotUnaryContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitNotUnary(Context\NotUnaryContext $context);

	/**
	 * Visit a parse tree produced by the `AddressOf` labeled alternative
	 * in {@see GolampiParser::unary()}.
	 *
	 * @param Context\AddressOfContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitAddressOf(Context\AddressOfContext $context);

	/**
	 * Visit a parse tree produced by the `Dereference` labeled alternative
	 * in {@see GolampiParser::unary()}.
	 *
	 * @param Context\DereferenceContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitDereference(Context\DereferenceContext $context);

	/**
	 * Visit a parse tree produced by the `IntLiteral` labeled alternative
	 * in {@see GolampiParser::primary()}.
	 *
	 * @param Context\IntLiteralContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitIntLiteral(Context\IntLiteralContext $context);

	/**
	 * Visit a parse tree produced by the `FloatLiteral` labeled alternative
	 * in {@see GolampiParser::primary()}.
	 *
	 * @param Context\FloatLiteralContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitFloatLiteral(Context\FloatLiteralContext $context);

	/**
	 * Visit a parse tree produced by the `RuneLiteral` labeled alternative
	 * in {@see GolampiParser::primary()}.
	 *
	 * @param Context\RuneLiteralContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitRuneLiteral(Context\RuneLiteralContext $context);

	/**
	 * Visit a parse tree produced by the `StringLiteral` labeled alternative
	 * in {@see GolampiParser::primary()}.
	 *
	 * @param Context\StringLiteralContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitStringLiteral(Context\StringLiteralContext $context);

	/**
	 * Visit a parse tree produced by the `TrueLiteral` labeled alternative
	 * in {@see GolampiParser::primary()}.
	 *
	 * @param Context\TrueLiteralContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitTrueLiteral(Context\TrueLiteralContext $context);

	/**
	 * Visit a parse tree produced by the `FalseLiteral` labeled alternative
	 * in {@see GolampiParser::primary()}.
	 *
	 * @param Context\FalseLiteralContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitFalseLiteral(Context\FalseLiteralContext $context);

	/**
	 * Visit a parse tree produced by the `NilLiteral` labeled alternative
	 * in {@see GolampiParser::primary()}.
	 *
	 * @param Context\NilLiteralContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitNilLiteral(Context\NilLiteralContext $context);

	/**
	 * Visit a parse tree produced by the `FunctionCall` labeled alternative
	 * in {@see GolampiParser::primary()}.
	 *
	 * @param Context\FunctionCallContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitFunctionCall(Context\FunctionCallContext $context);

	/**
	 * Visit a parse tree produced by the `ArrayAccess` labeled alternative
	 * in {@see GolampiParser::primary()}.
	 *
	 * @param Context\ArrayAccessContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitArrayAccess(Context\ArrayAccessContext $context);

	/**
	 * Visit a parse tree produced by the `Identifier` labeled alternative
	 * in {@see GolampiParser::primary()}.
	 *
	 * @param Context\IdentifierContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitIdentifier(Context\IdentifierContext $context);

	/**
	 * Visit a parse tree produced by the `GroupedExpression` labeled alternative
	 * in {@see GolampiParser::primary()}.
	 *
	 * @param Context\GroupedExpressionContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitGroupedExpression(Context\GroupedExpressionContext $context);

	/**
	 * Visit a parse tree produced by the `ArrayLiteralExpr` labeled alternative
	 * in {@see GolampiParser::primary()}.
	 *
	 * @param Context\ArrayLiteralExprContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitArrayLiteralExpr(Context\ArrayLiteralExprContext $context);

	/**
	 * Visit a parse tree produced by the `InnerArrayLiteral` labeled alternative
	 * in {@see GolampiParser::primary()}.
	 *
	 * @param Context\InnerArrayLiteralContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitInnerArrayLiteral(Context\InnerArrayLiteralContext $context);

	/**
	 * Visit a parse tree produced by the `FixedArrayLiteralNode` labeled alternative
	 * in {@see GolampiParser::arrayLiteral()}.
	 *
	 * @param Context\FixedArrayLiteralNodeContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitFixedArrayLiteralNode(Context\FixedArrayLiteralNodeContext $context);

	/**
	 * Visit a parse tree produced by the `SliceLiteralNode` labeled alternative
	 * in {@see GolampiParser::arrayLiteral()}.
	 *
	 * @param Context\SliceLiteralNodeContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitSliceLiteralNode(Context\SliceLiteralNodeContext $context);

	/**
	 * Visit a parse tree produced by {@see GolampiParser::innerLiteralList()}.
	 *
	 * @param Context\InnerLiteralListContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitInnerLiteralList(Context\InnerLiteralListContext $context);

	/**
	 * Visit a parse tree produced by {@see GolampiParser::innerLiteral()}.
	 *
	 * @param Context\InnerLiteralContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitInnerLiteral(Context\InnerLiteralContext $context);

	/**
	 * Visit a parse tree produced by {@see GolampiParser::argumentList()}.
	 *
	 * @param Context\ArgumentListContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitArgumentList(Context\ArgumentListContext $context);

	/**
	 * Visit a parse tree produced by the `ExpressionArgument` labeled alternative
	 * in {@see GolampiParser::argument()}.
	 *
	 * @param Context\ExpressionArgumentContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitExpressionArgument(Context\ExpressionArgumentContext $context);

	/**
	 * Visit a parse tree produced by the `AddressArgument` labeled alternative
	 * in {@see GolampiParser::argument()}.
	 *
	 * @param Context\AddressArgumentContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitAddressArgument(Context\AddressArgumentContext $context);

	/**
	 * Visit a parse tree produced by the `Int32Type` labeled alternative
	 * in {@see GolampiParser::type()}.
	 *
	 * @param Context\Int32TypeContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitInt32Type(Context\Int32TypeContext $context);

	/**
	 * Visit a parse tree produced by the `Float32Type` labeled alternative
	 * in {@see GolampiParser::type()}.
	 *
	 * @param Context\Float32TypeContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitFloat32Type(Context\Float32TypeContext $context);

	/**
	 * Visit a parse tree produced by the `BoolType` labeled alternative
	 * in {@see GolampiParser::type()}.
	 *
	 * @param Context\BoolTypeContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitBoolType(Context\BoolTypeContext $context);

	/**
	 * Visit a parse tree produced by the `RuneType` labeled alternative
	 * in {@see GolampiParser::type()}.
	 *
	 * @param Context\RuneTypeContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitRuneType(Context\RuneTypeContext $context);

	/**
	 * Visit a parse tree produced by the `StringType` labeled alternative
	 * in {@see GolampiParser::type()}.
	 *
	 * @param Context\StringTypeContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitStringType(Context\StringTypeContext $context);

	/**
	 * Visit a parse tree produced by the `ArrayType` labeled alternative
	 * in {@see GolampiParser::type()}.
	 *
	 * @param Context\ArrayTypeContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitArrayType(Context\ArrayTypeContext $context);

	/**
	 * Visit a parse tree produced by the `SliceType` labeled alternative
	 * in {@see GolampiParser::type()}.
	 *
	 * @param Context\SliceTypeContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitSliceType(Context\SliceTypeContext $context);

	/**
	 * Visit a parse tree produced by the `PointerType` labeled alternative
	 * in {@see GolampiParser::type()}.
	 *
	 * @param Context\PointerTypeContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitPointerType(Context\PointerTypeContext $context);
}