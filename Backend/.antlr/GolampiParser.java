// Generated from /home/isai/Documentos/Github/OLC2_Proyecto1_202308204/Backend/Golampi.g4 by ANTLR 4.13.1
import org.antlr.v4.runtime.atn.*;
import org.antlr.v4.runtime.dfa.DFA;
import org.antlr.v4.runtime.*;
import org.antlr.v4.runtime.misc.*;
import org.antlr.v4.runtime.tree.*;
import java.util.List;
import java.util.Iterator;
import java.util.ArrayList;

@SuppressWarnings({"all", "warnings", "unchecked", "unused", "cast", "CheckReturnValue"})
public class GolampiParser extends Parser {
	static { RuntimeMetaData.checkVersion("4.13.1", RuntimeMetaData.VERSION); }

	protected static final DFA[] _decisionToDFA;
	protected static final PredictionContextCache _sharedContextCache =
		new PredictionContextCache();
	public static final int
		T__0=1, T__1=2, T__2=3, T__3=4, T__4=5, T__5=6, T__6=7, T__7=8, T__8=9, 
		T__9=10, T__10=11, T__11=12, T__12=13, T__13=14, T__14=15, T__15=16, T__16=17, 
		T__17=18, T__18=19, T__19=20, T__20=21, T__21=22, T__22=23, T__23=24, 
		T__24=25, T__25=26, T__26=27, T__27=28, T__28=29, T__29=30, T__30=31, 
		VAR=32, CONST=33, FUNC=34, IF=35, ELSE=36, SWITCH=37, CASE=38, DEFAULT=39, 
		FOR=40, BREAK=41, CONTINUE=42, RETURN=43, TRUE=44, FALSE=45, NIL=46, INT32_TYPE=47, 
		FLOAT32_TYPE=48, BOOL_TYPE=49, RUNE_TYPE=50, STRING_TYPE=51, AND=52, OR=53, 
		INT32=54, FLOAT32=55, RUNE=56, STRING=57, ID=58, LINE_COMMENT=59, BLOCK_COMMENT=60, 
		WS=61;
	public static final int
		RULE_program = 0, RULE_declaration = 1, RULE_varDeclaration = 2, RULE_shortVarDeclaration = 3, 
		RULE_constDeclaration = 4, RULE_functionDeclaration = 5, RULE_parameterList = 6, 
		RULE_parameter = 7, RULE_typeList = 8, RULE_idList = 9, RULE_expressionList = 10, 
		RULE_statement = 11, RULE_assignment = 12, RULE_assignOp = 13, RULE_incDecStatement = 14, 
		RULE_ifStatement = 15, RULE_switchStatement = 16, RULE_caseClause = 17, 
		RULE_defaultClause = 18, RULE_forStatement = 19, RULE_forClause = 20, 
		RULE_forInit = 21, RULE_forPost = 22, RULE_breakStatement = 23, RULE_continueStatement = 24, 
		RULE_returnStatement = 25, RULE_block = 26, RULE_expressionStatement = 27, 
		RULE_expression = 28, RULE_logicalOr = 29, RULE_logicalAnd = 30, RULE_equality = 31, 
		RULE_relational = 32, RULE_additive = 33, RULE_multiplicative = 34, RULE_unary = 35, 
		RULE_primary = 36, RULE_arrayLiteral = 37, RULE_innerLiteralList = 38, 
		RULE_innerLiteral = 39, RULE_argumentList = 40, RULE_argument = 41, RULE_type = 42;
	private static String[] makeRuleNames() {
		return new String[] {
			"program", "declaration", "varDeclaration", "shortVarDeclaration", "constDeclaration", 
			"functionDeclaration", "parameterList", "parameter", "typeList", "idList", 
			"expressionList", "statement", "assignment", "assignOp", "incDecStatement", 
			"ifStatement", "switchStatement", "caseClause", "defaultClause", "forStatement", 
			"forClause", "forInit", "forPost", "breakStatement", "continueStatement", 
			"returnStatement", "block", "expressionStatement", "expression", "logicalOr", 
			"logicalAnd", "equality", "relational", "additive", "multiplicative", 
			"unary", "primary", "arrayLiteral", "innerLiteralList", "innerLiteral", 
			"argumentList", "argument", "type"
		};
	}
	public static final String[] ruleNames = makeRuleNames();

	private static String[] makeLiteralNames() {
		return new String[] {
			null, "'='", "':='", "'('", "')'", "','", "'*'", "'['", "']'", "'+='", 
			"'-='", "'*='", "'/='", "'++'", "'--'", "'{'", "'}'", "':'", "';'", "'=='", 
			"'!='", "'>'", "'>='", "'<'", "'<='", "'+'", "'-'", "'/'", "'%'", "'!'", 
			"'&'", "'.'", "'var'", "'const'", "'func'", "'if'", "'else'", "'switch'", 
			"'case'", "'default'", "'for'", "'break'", "'continue'", "'return'", 
			"'true'", "'false'", "'nil'", "'int32'", "'float32'", "'bool'", "'rune'", 
			"'string'", "'&&'", "'||'"
		};
	}
	private static final String[] _LITERAL_NAMES = makeLiteralNames();
	private static String[] makeSymbolicNames() {
		return new String[] {
			null, null, null, null, null, null, null, null, null, null, null, null, 
			null, null, null, null, null, null, null, null, null, null, null, null, 
			null, null, null, null, null, null, null, null, "VAR", "CONST", "FUNC", 
			"IF", "ELSE", "SWITCH", "CASE", "DEFAULT", "FOR", "BREAK", "CONTINUE", 
			"RETURN", "TRUE", "FALSE", "NIL", "INT32_TYPE", "FLOAT32_TYPE", "BOOL_TYPE", 
			"RUNE_TYPE", "STRING_TYPE", "AND", "OR", "INT32", "FLOAT32", "RUNE", 
			"STRING", "ID", "LINE_COMMENT", "BLOCK_COMMENT", "WS"
		};
	}
	private static final String[] _SYMBOLIC_NAMES = makeSymbolicNames();
	public static final Vocabulary VOCABULARY = new VocabularyImpl(_LITERAL_NAMES, _SYMBOLIC_NAMES);

	/**
	 * @deprecated Use {@link #VOCABULARY} instead.
	 */
	@Deprecated
	public static final String[] tokenNames;
	static {
		tokenNames = new String[_SYMBOLIC_NAMES.length];
		for (int i = 0; i < tokenNames.length; i++) {
			tokenNames[i] = VOCABULARY.getLiteralName(i);
			if (tokenNames[i] == null) {
				tokenNames[i] = VOCABULARY.getSymbolicName(i);
			}

			if (tokenNames[i] == null) {
				tokenNames[i] = "<INVALID>";
			}
		}
	}

	@Override
	@Deprecated
	public String[] getTokenNames() {
		return tokenNames;
	}

	@Override

	public Vocabulary getVocabulary() {
		return VOCABULARY;
	}

	@Override
	public String getGrammarFileName() { return "Golampi.g4"; }

	@Override
	public String[] getRuleNames() { return ruleNames; }

	@Override
	public String getSerializedATN() { return _serializedATN; }

	@Override
	public ATN getATN() { return _ATN; }

	public GolampiParser(TokenStream input) {
		super(input);
		_interp = new ParserATNSimulator(this,_ATN,_decisionToDFA,_sharedContextCache);
	}

	@SuppressWarnings("CheckReturnValue")
	public static class ProgramContext extends ParserRuleContext {
		public TerminalNode EOF() { return getToken(GolampiParser.EOF, 0); }
		public List<DeclarationContext> declaration() {
			return getRuleContexts(DeclarationContext.class);
		}
		public DeclarationContext declaration(int i) {
			return getRuleContext(DeclarationContext.class,i);
		}
		public ProgramContext(ParserRuleContext parent, int invokingState) {
			super(parent, invokingState);
		}
		@Override public int getRuleIndex() { return RULE_program; }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterProgram(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitProgram(this);
		}
	}

	public final ProgramContext program() throws RecognitionException {
		ProgramContext _localctx = new ProgramContext(_ctx, getState());
		enterRule(_localctx, 0, RULE_program);
		int _la;
		try {
			enterOuterAlt(_localctx, 1);
			{
			setState(89);
			_errHandler.sync(this);
			_la = _input.LA(1);
			while ((((_la) & ~0x3f) == 0 && ((1L << _la) & 558586195311886536L) != 0)) {
				{
				{
				setState(86);
				declaration();
				}
				}
				setState(91);
				_errHandler.sync(this);
				_la = _input.LA(1);
			}
			setState(92);
			match(EOF);
			}
		}
		catch (RecognitionException re) {
			_localctx.exception = re;
			_errHandler.reportError(this, re);
			_errHandler.recover(this, re);
		}
		finally {
			exitRule();
		}
		return _localctx;
	}

	@SuppressWarnings("CheckReturnValue")
	public static class DeclarationContext extends ParserRuleContext {
		public VarDeclarationContext varDeclaration() {
			return getRuleContext(VarDeclarationContext.class,0);
		}
		public ConstDeclarationContext constDeclaration() {
			return getRuleContext(ConstDeclarationContext.class,0);
		}
		public FunctionDeclarationContext functionDeclaration() {
			return getRuleContext(FunctionDeclarationContext.class,0);
		}
		public StatementContext statement() {
			return getRuleContext(StatementContext.class,0);
		}
		public DeclarationContext(ParserRuleContext parent, int invokingState) {
			super(parent, invokingState);
		}
		@Override public int getRuleIndex() { return RULE_declaration; }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterDeclaration(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitDeclaration(this);
		}
	}

	public final DeclarationContext declaration() throws RecognitionException {
		DeclarationContext _localctx = new DeclarationContext(_ctx, getState());
		enterRule(_localctx, 2, RULE_declaration);
		try {
			setState(98);
			_errHandler.sync(this);
			switch (_input.LA(1)) {
			case VAR:
				enterOuterAlt(_localctx, 1);
				{
				setState(94);
				varDeclaration();
				}
				break;
			case CONST:
				enterOuterAlt(_localctx, 2);
				{
				setState(95);
				constDeclaration();
				}
				break;
			case FUNC:
				enterOuterAlt(_localctx, 3);
				{
				setState(96);
				functionDeclaration();
				}
				break;
			case T__2:
			case T__5:
			case T__6:
			case T__14:
			case T__25:
			case T__28:
			case T__29:
			case IF:
			case SWITCH:
			case FOR:
			case BREAK:
			case CONTINUE:
			case RETURN:
			case TRUE:
			case FALSE:
			case NIL:
			case INT32:
			case FLOAT32:
			case RUNE:
			case STRING:
			case ID:
				enterOuterAlt(_localctx, 4);
				{
				setState(97);
				statement();
				}
				break;
			default:
				throw new NoViableAltException(this);
			}
		}
		catch (RecognitionException re) {
			_localctx.exception = re;
			_errHandler.reportError(this, re);
			_errHandler.recover(this, re);
		}
		finally {
			exitRule();
		}
		return _localctx;
	}

	@SuppressWarnings("CheckReturnValue")
	public static class VarDeclarationContext extends ParserRuleContext {
		public VarDeclarationContext(ParserRuleContext parent, int invokingState) {
			super(parent, invokingState);
		}
		@Override public int getRuleIndex() { return RULE_varDeclaration; }
	 
		public VarDeclarationContext() { }
		public void copyFrom(VarDeclarationContext ctx) {
			super.copyFrom(ctx);
		}
	}
	@SuppressWarnings("CheckReturnValue")
	public static class VarDeclSimpleContext extends VarDeclarationContext {
		public TerminalNode VAR() { return getToken(GolampiParser.VAR, 0); }
		public IdListContext idList() {
			return getRuleContext(IdListContext.class,0);
		}
		public TypeContext type() {
			return getRuleContext(TypeContext.class,0);
		}
		public VarDeclSimpleContext(VarDeclarationContext ctx) { copyFrom(ctx); }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterVarDeclSimple(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitVarDeclSimple(this);
		}
	}
	@SuppressWarnings("CheckReturnValue")
	public static class VarDeclWithInitContext extends VarDeclarationContext {
		public TerminalNode VAR() { return getToken(GolampiParser.VAR, 0); }
		public IdListContext idList() {
			return getRuleContext(IdListContext.class,0);
		}
		public TypeContext type() {
			return getRuleContext(TypeContext.class,0);
		}
		public ExpressionListContext expressionList() {
			return getRuleContext(ExpressionListContext.class,0);
		}
		public VarDeclWithInitContext(VarDeclarationContext ctx) { copyFrom(ctx); }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterVarDeclWithInit(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitVarDeclWithInit(this);
		}
	}

	public final VarDeclarationContext varDeclaration() throws RecognitionException {
		VarDeclarationContext _localctx = new VarDeclarationContext(_ctx, getState());
		enterRule(_localctx, 4, RULE_varDeclaration);
		try {
			setState(110);
			_errHandler.sync(this);
			switch ( getInterpreter().adaptivePredict(_input,2,_ctx) ) {
			case 1:
				_localctx = new VarDeclSimpleContext(_localctx);
				enterOuterAlt(_localctx, 1);
				{
				setState(100);
				match(VAR);
				setState(101);
				idList();
				setState(102);
				type();
				}
				break;
			case 2:
				_localctx = new VarDeclWithInitContext(_localctx);
				enterOuterAlt(_localctx, 2);
				{
				setState(104);
				match(VAR);
				setState(105);
				idList();
				setState(106);
				type();
				setState(107);
				match(T__0);
				setState(108);
				expressionList();
				}
				break;
			}
		}
		catch (RecognitionException re) {
			_localctx.exception = re;
			_errHandler.reportError(this, re);
			_errHandler.recover(this, re);
		}
		finally {
			exitRule();
		}
		return _localctx;
	}

	@SuppressWarnings("CheckReturnValue")
	public static class ShortVarDeclarationContext extends ParserRuleContext {
		public ShortVarDeclarationContext(ParserRuleContext parent, int invokingState) {
			super(parent, invokingState);
		}
		@Override public int getRuleIndex() { return RULE_shortVarDeclaration; }
	 
		public ShortVarDeclarationContext() { }
		public void copyFrom(ShortVarDeclarationContext ctx) {
			super.copyFrom(ctx);
		}
	}
	@SuppressWarnings("CheckReturnValue")
	public static class ShortVarDeclContext extends ShortVarDeclarationContext {
		public IdListContext idList() {
			return getRuleContext(IdListContext.class,0);
		}
		public ExpressionListContext expressionList() {
			return getRuleContext(ExpressionListContext.class,0);
		}
		public ShortVarDeclContext(ShortVarDeclarationContext ctx) { copyFrom(ctx); }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterShortVarDecl(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitShortVarDecl(this);
		}
	}

	public final ShortVarDeclarationContext shortVarDeclaration() throws RecognitionException {
		ShortVarDeclarationContext _localctx = new ShortVarDeclarationContext(_ctx, getState());
		enterRule(_localctx, 6, RULE_shortVarDeclaration);
		try {
			_localctx = new ShortVarDeclContext(_localctx);
			enterOuterAlt(_localctx, 1);
			{
			setState(112);
			idList();
			setState(113);
			match(T__1);
			setState(114);
			expressionList();
			}
		}
		catch (RecognitionException re) {
			_localctx.exception = re;
			_errHandler.reportError(this, re);
			_errHandler.recover(this, re);
		}
		finally {
			exitRule();
		}
		return _localctx;
	}

	@SuppressWarnings("CheckReturnValue")
	public static class ConstDeclarationContext extends ParserRuleContext {
		public ConstDeclarationContext(ParserRuleContext parent, int invokingState) {
			super(parent, invokingState);
		}
		@Override public int getRuleIndex() { return RULE_constDeclaration; }
	 
		public ConstDeclarationContext() { }
		public void copyFrom(ConstDeclarationContext ctx) {
			super.copyFrom(ctx);
		}
	}
	@SuppressWarnings("CheckReturnValue")
	public static class ConstDeclContext extends ConstDeclarationContext {
		public TerminalNode CONST() { return getToken(GolampiParser.CONST, 0); }
		public TerminalNode ID() { return getToken(GolampiParser.ID, 0); }
		public TypeContext type() {
			return getRuleContext(TypeContext.class,0);
		}
		public ExpressionContext expression() {
			return getRuleContext(ExpressionContext.class,0);
		}
		public ConstDeclContext(ConstDeclarationContext ctx) { copyFrom(ctx); }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterConstDecl(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitConstDecl(this);
		}
	}

	public final ConstDeclarationContext constDeclaration() throws RecognitionException {
		ConstDeclarationContext _localctx = new ConstDeclarationContext(_ctx, getState());
		enterRule(_localctx, 8, RULE_constDeclaration);
		try {
			_localctx = new ConstDeclContext(_localctx);
			enterOuterAlt(_localctx, 1);
			{
			setState(116);
			match(CONST);
			setState(117);
			match(ID);
			setState(118);
			type();
			setState(119);
			match(T__0);
			setState(120);
			expression();
			}
		}
		catch (RecognitionException re) {
			_localctx.exception = re;
			_errHandler.reportError(this, re);
			_errHandler.recover(this, re);
		}
		finally {
			exitRule();
		}
		return _localctx;
	}

	@SuppressWarnings("CheckReturnValue")
	public static class FunctionDeclarationContext extends ParserRuleContext {
		public FunctionDeclarationContext(ParserRuleContext parent, int invokingState) {
			super(parent, invokingState);
		}
		@Override public int getRuleIndex() { return RULE_functionDeclaration; }
	 
		public FunctionDeclarationContext() { }
		public void copyFrom(FunctionDeclarationContext ctx) {
			super.copyFrom(ctx);
		}
	}
	@SuppressWarnings("CheckReturnValue")
	public static class FuncDeclSingleReturnContext extends FunctionDeclarationContext {
		public TerminalNode FUNC() { return getToken(GolampiParser.FUNC, 0); }
		public TerminalNode ID() { return getToken(GolampiParser.ID, 0); }
		public BlockContext block() {
			return getRuleContext(BlockContext.class,0);
		}
		public ParameterListContext parameterList() {
			return getRuleContext(ParameterListContext.class,0);
		}
		public TypeContext type() {
			return getRuleContext(TypeContext.class,0);
		}
		public FuncDeclSingleReturnContext(FunctionDeclarationContext ctx) { copyFrom(ctx); }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterFuncDeclSingleReturn(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitFuncDeclSingleReturn(this);
		}
	}
	@SuppressWarnings("CheckReturnValue")
	public static class FuncDeclMultiReturnContext extends FunctionDeclarationContext {
		public TerminalNode FUNC() { return getToken(GolampiParser.FUNC, 0); }
		public TerminalNode ID() { return getToken(GolampiParser.ID, 0); }
		public TypeListContext typeList() {
			return getRuleContext(TypeListContext.class,0);
		}
		public BlockContext block() {
			return getRuleContext(BlockContext.class,0);
		}
		public ParameterListContext parameterList() {
			return getRuleContext(ParameterListContext.class,0);
		}
		public FuncDeclMultiReturnContext(FunctionDeclarationContext ctx) { copyFrom(ctx); }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterFuncDeclMultiReturn(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitFuncDeclMultiReturn(this);
		}
	}

	public final FunctionDeclarationContext functionDeclaration() throws RecognitionException {
		FunctionDeclarationContext _localctx = new FunctionDeclarationContext(_ctx, getState());
		enterRule(_localctx, 10, RULE_functionDeclaration);
		int _la;
		try {
			setState(145);
			_errHandler.sync(this);
			switch ( getInterpreter().adaptivePredict(_input,6,_ctx) ) {
			case 1:
				_localctx = new FuncDeclSingleReturnContext(_localctx);
				enterOuterAlt(_localctx, 1);
				{
				setState(122);
				match(FUNC);
				setState(123);
				match(ID);
				setState(124);
				match(T__2);
				setState(126);
				_errHandler.sync(this);
				_la = _input.LA(1);
				if (_la==T__5 || _la==ID) {
					{
					setState(125);
					parameterList();
					}
				}

				setState(128);
				match(T__3);
				setState(130);
				_errHandler.sync(this);
				_la = _input.LA(1);
				if ((((_la) & ~0x3f) == 0 && ((1L << _la) & 4362862139015360L) != 0)) {
					{
					setState(129);
					type();
					}
				}

				setState(132);
				block();
				}
				break;
			case 2:
				_localctx = new FuncDeclMultiReturnContext(_localctx);
				enterOuterAlt(_localctx, 2);
				{
				setState(133);
				match(FUNC);
				setState(134);
				match(ID);
				setState(135);
				match(T__2);
				setState(137);
				_errHandler.sync(this);
				_la = _input.LA(1);
				if (_la==T__5 || _la==ID) {
					{
					setState(136);
					parameterList();
					}
				}

				setState(139);
				match(T__3);
				setState(140);
				match(T__2);
				setState(141);
				typeList();
				setState(142);
				match(T__3);
				setState(143);
				block();
				}
				break;
			}
		}
		catch (RecognitionException re) {
			_localctx.exception = re;
			_errHandler.reportError(this, re);
			_errHandler.recover(this, re);
		}
		finally {
			exitRule();
		}
		return _localctx;
	}

	@SuppressWarnings("CheckReturnValue")
	public static class ParameterListContext extends ParserRuleContext {
		public List<ParameterContext> parameter() {
			return getRuleContexts(ParameterContext.class);
		}
		public ParameterContext parameter(int i) {
			return getRuleContext(ParameterContext.class,i);
		}
		public ParameterListContext(ParserRuleContext parent, int invokingState) {
			super(parent, invokingState);
		}
		@Override public int getRuleIndex() { return RULE_parameterList; }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterParameterList(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitParameterList(this);
		}
	}

	public final ParameterListContext parameterList() throws RecognitionException {
		ParameterListContext _localctx = new ParameterListContext(_ctx, getState());
		enterRule(_localctx, 12, RULE_parameterList);
		int _la;
		try {
			enterOuterAlt(_localctx, 1);
			{
			setState(147);
			parameter();
			setState(152);
			_errHandler.sync(this);
			_la = _input.LA(1);
			while (_la==T__4) {
				{
				{
				setState(148);
				match(T__4);
				setState(149);
				parameter();
				}
				}
				setState(154);
				_errHandler.sync(this);
				_la = _input.LA(1);
			}
			}
		}
		catch (RecognitionException re) {
			_localctx.exception = re;
			_errHandler.reportError(this, re);
			_errHandler.recover(this, re);
		}
		finally {
			exitRule();
		}
		return _localctx;
	}

	@SuppressWarnings("CheckReturnValue")
	public static class ParameterContext extends ParserRuleContext {
		public ParameterContext(ParserRuleContext parent, int invokingState) {
			super(parent, invokingState);
		}
		@Override public int getRuleIndex() { return RULE_parameter; }
	 
		public ParameterContext() { }
		public void copyFrom(ParameterContext ctx) {
			super.copyFrom(ctx);
		}
	}
	@SuppressWarnings("CheckReturnValue")
	public static class NormalParameterContext extends ParameterContext {
		public TerminalNode ID() { return getToken(GolampiParser.ID, 0); }
		public TypeContext type() {
			return getRuleContext(TypeContext.class,0);
		}
		public NormalParameterContext(ParameterContext ctx) { copyFrom(ctx); }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterNormalParameter(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitNormalParameter(this);
		}
	}
	@SuppressWarnings("CheckReturnValue")
	public static class PointerParameterContext extends ParameterContext {
		public TerminalNode ID() { return getToken(GolampiParser.ID, 0); }
		public TypeContext type() {
			return getRuleContext(TypeContext.class,0);
		}
		public PointerParameterContext(ParameterContext ctx) { copyFrom(ctx); }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterPointerParameter(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitPointerParameter(this);
		}
	}

	public final ParameterContext parameter() throws RecognitionException {
		ParameterContext _localctx = new ParameterContext(_ctx, getState());
		enterRule(_localctx, 14, RULE_parameter);
		try {
			setState(160);
			_errHandler.sync(this);
			switch (_input.LA(1)) {
			case ID:
				_localctx = new NormalParameterContext(_localctx);
				enterOuterAlt(_localctx, 1);
				{
				setState(155);
				match(ID);
				setState(156);
				type();
				}
				break;
			case T__5:
				_localctx = new PointerParameterContext(_localctx);
				enterOuterAlt(_localctx, 2);
				{
				setState(157);
				match(T__5);
				setState(158);
				match(ID);
				setState(159);
				type();
				}
				break;
			default:
				throw new NoViableAltException(this);
			}
		}
		catch (RecognitionException re) {
			_localctx.exception = re;
			_errHandler.reportError(this, re);
			_errHandler.recover(this, re);
		}
		finally {
			exitRule();
		}
		return _localctx;
	}

	@SuppressWarnings("CheckReturnValue")
	public static class TypeListContext extends ParserRuleContext {
		public List<TypeContext> type() {
			return getRuleContexts(TypeContext.class);
		}
		public TypeContext type(int i) {
			return getRuleContext(TypeContext.class,i);
		}
		public TypeListContext(ParserRuleContext parent, int invokingState) {
			super(parent, invokingState);
		}
		@Override public int getRuleIndex() { return RULE_typeList; }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterTypeList(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitTypeList(this);
		}
	}

	public final TypeListContext typeList() throws RecognitionException {
		TypeListContext _localctx = new TypeListContext(_ctx, getState());
		enterRule(_localctx, 16, RULE_typeList);
		int _la;
		try {
			enterOuterAlt(_localctx, 1);
			{
			setState(162);
			type();
			setState(167);
			_errHandler.sync(this);
			_la = _input.LA(1);
			while (_la==T__4) {
				{
				{
				setState(163);
				match(T__4);
				setState(164);
				type();
				}
				}
				setState(169);
				_errHandler.sync(this);
				_la = _input.LA(1);
			}
			}
		}
		catch (RecognitionException re) {
			_localctx.exception = re;
			_errHandler.reportError(this, re);
			_errHandler.recover(this, re);
		}
		finally {
			exitRule();
		}
		return _localctx;
	}

	@SuppressWarnings("CheckReturnValue")
	public static class IdListContext extends ParserRuleContext {
		public List<TerminalNode> ID() { return getTokens(GolampiParser.ID); }
		public TerminalNode ID(int i) {
			return getToken(GolampiParser.ID, i);
		}
		public IdListContext(ParserRuleContext parent, int invokingState) {
			super(parent, invokingState);
		}
		@Override public int getRuleIndex() { return RULE_idList; }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterIdList(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitIdList(this);
		}
	}

	public final IdListContext idList() throws RecognitionException {
		IdListContext _localctx = new IdListContext(_ctx, getState());
		enterRule(_localctx, 18, RULE_idList);
		int _la;
		try {
			enterOuterAlt(_localctx, 1);
			{
			setState(170);
			match(ID);
			setState(175);
			_errHandler.sync(this);
			_la = _input.LA(1);
			while (_la==T__4) {
				{
				{
				setState(171);
				match(T__4);
				setState(172);
				match(ID);
				}
				}
				setState(177);
				_errHandler.sync(this);
				_la = _input.LA(1);
			}
			}
		}
		catch (RecognitionException re) {
			_localctx.exception = re;
			_errHandler.reportError(this, re);
			_errHandler.recover(this, re);
		}
		finally {
			exitRule();
		}
		return _localctx;
	}

	@SuppressWarnings("CheckReturnValue")
	public static class ExpressionListContext extends ParserRuleContext {
		public List<ExpressionContext> expression() {
			return getRuleContexts(ExpressionContext.class);
		}
		public ExpressionContext expression(int i) {
			return getRuleContext(ExpressionContext.class,i);
		}
		public ExpressionListContext(ParserRuleContext parent, int invokingState) {
			super(parent, invokingState);
		}
		@Override public int getRuleIndex() { return RULE_expressionList; }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterExpressionList(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitExpressionList(this);
		}
	}

	public final ExpressionListContext expressionList() throws RecognitionException {
		ExpressionListContext _localctx = new ExpressionListContext(_ctx, getState());
		enterRule(_localctx, 20, RULE_expressionList);
		try {
			int _alt;
			enterOuterAlt(_localctx, 1);
			{
			setState(178);
			expression();
			setState(183);
			_errHandler.sync(this);
			_alt = getInterpreter().adaptivePredict(_input,11,_ctx);
			while ( _alt!=2 && _alt!=org.antlr.v4.runtime.atn.ATN.INVALID_ALT_NUMBER ) {
				if ( _alt==1 ) {
					{
					{
					setState(179);
					match(T__4);
					setState(180);
					expression();
					}
					} 
				}
				setState(185);
				_errHandler.sync(this);
				_alt = getInterpreter().adaptivePredict(_input,11,_ctx);
			}
			}
		}
		catch (RecognitionException re) {
			_localctx.exception = re;
			_errHandler.reportError(this, re);
			_errHandler.recover(this, re);
		}
		finally {
			exitRule();
		}
		return _localctx;
	}

	@SuppressWarnings("CheckReturnValue")
	public static class StatementContext extends ParserRuleContext {
		public ShortVarDeclarationContext shortVarDeclaration() {
			return getRuleContext(ShortVarDeclarationContext.class,0);
		}
		public AssignmentContext assignment() {
			return getRuleContext(AssignmentContext.class,0);
		}
		public IfStatementContext ifStatement() {
			return getRuleContext(IfStatementContext.class,0);
		}
		public SwitchStatementContext switchStatement() {
			return getRuleContext(SwitchStatementContext.class,0);
		}
		public ForStatementContext forStatement() {
			return getRuleContext(ForStatementContext.class,0);
		}
		public BreakStatementContext breakStatement() {
			return getRuleContext(BreakStatementContext.class,0);
		}
		public ContinueStatementContext continueStatement() {
			return getRuleContext(ContinueStatementContext.class,0);
		}
		public ReturnStatementContext returnStatement() {
			return getRuleContext(ReturnStatementContext.class,0);
		}
		public IncDecStatementContext incDecStatement() {
			return getRuleContext(IncDecStatementContext.class,0);
		}
		public BlockContext block() {
			return getRuleContext(BlockContext.class,0);
		}
		public ExpressionStatementContext expressionStatement() {
			return getRuleContext(ExpressionStatementContext.class,0);
		}
		public StatementContext(ParserRuleContext parent, int invokingState) {
			super(parent, invokingState);
		}
		@Override public int getRuleIndex() { return RULE_statement; }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterStatement(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitStatement(this);
		}
	}

	public final StatementContext statement() throws RecognitionException {
		StatementContext _localctx = new StatementContext(_ctx, getState());
		enterRule(_localctx, 22, RULE_statement);
		try {
			setState(197);
			_errHandler.sync(this);
			switch ( getInterpreter().adaptivePredict(_input,12,_ctx) ) {
			case 1:
				enterOuterAlt(_localctx, 1);
				{
				setState(186);
				shortVarDeclaration();
				}
				break;
			case 2:
				enterOuterAlt(_localctx, 2);
				{
				setState(187);
				assignment();
				}
				break;
			case 3:
				enterOuterAlt(_localctx, 3);
				{
				setState(188);
				ifStatement();
				}
				break;
			case 4:
				enterOuterAlt(_localctx, 4);
				{
				setState(189);
				switchStatement();
				}
				break;
			case 5:
				enterOuterAlt(_localctx, 5);
				{
				setState(190);
				forStatement();
				}
				break;
			case 6:
				enterOuterAlt(_localctx, 6);
				{
				setState(191);
				breakStatement();
				}
				break;
			case 7:
				enterOuterAlt(_localctx, 7);
				{
				setState(192);
				continueStatement();
				}
				break;
			case 8:
				enterOuterAlt(_localctx, 8);
				{
				setState(193);
				returnStatement();
				}
				break;
			case 9:
				enterOuterAlt(_localctx, 9);
				{
				setState(194);
				incDecStatement();
				}
				break;
			case 10:
				enterOuterAlt(_localctx, 10);
				{
				setState(195);
				block();
				}
				break;
			case 11:
				enterOuterAlt(_localctx, 11);
				{
				setState(196);
				expressionStatement();
				}
				break;
			}
		}
		catch (RecognitionException re) {
			_localctx.exception = re;
			_errHandler.reportError(this, re);
			_errHandler.recover(this, re);
		}
		finally {
			exitRule();
		}
		return _localctx;
	}

	@SuppressWarnings("CheckReturnValue")
	public static class AssignmentContext extends ParserRuleContext {
		public AssignmentContext(ParserRuleContext parent, int invokingState) {
			super(parent, invokingState);
		}
		@Override public int getRuleIndex() { return RULE_assignment; }
	 
		public AssignmentContext() { }
		public void copyFrom(AssignmentContext ctx) {
			super.copyFrom(ctx);
		}
	}
	@SuppressWarnings("CheckReturnValue")
	public static class PointerAssignmentContext extends AssignmentContext {
		public TerminalNode ID() { return getToken(GolampiParser.ID, 0); }
		public AssignOpContext assignOp() {
			return getRuleContext(AssignOpContext.class,0);
		}
		public ExpressionContext expression() {
			return getRuleContext(ExpressionContext.class,0);
		}
		public PointerAssignmentContext(AssignmentContext ctx) { copyFrom(ctx); }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterPointerAssignment(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitPointerAssignment(this);
		}
	}
	@SuppressWarnings("CheckReturnValue")
	public static class SimpleAssignmentContext extends AssignmentContext {
		public TerminalNode ID() { return getToken(GolampiParser.ID, 0); }
		public AssignOpContext assignOp() {
			return getRuleContext(AssignOpContext.class,0);
		}
		public ExpressionContext expression() {
			return getRuleContext(ExpressionContext.class,0);
		}
		public SimpleAssignmentContext(AssignmentContext ctx) { copyFrom(ctx); }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterSimpleAssignment(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitSimpleAssignment(this);
		}
	}
	@SuppressWarnings("CheckReturnValue")
	public static class ArrayAssignmentContext extends AssignmentContext {
		public TerminalNode ID() { return getToken(GolampiParser.ID, 0); }
		public AssignOpContext assignOp() {
			return getRuleContext(AssignOpContext.class,0);
		}
		public List<ExpressionContext> expression() {
			return getRuleContexts(ExpressionContext.class);
		}
		public ExpressionContext expression(int i) {
			return getRuleContext(ExpressionContext.class,i);
		}
		public ArrayAssignmentContext(AssignmentContext ctx) { copyFrom(ctx); }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterArrayAssignment(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitArrayAssignment(this);
		}
	}

	public final AssignmentContext assignment() throws RecognitionException {
		AssignmentContext _localctx = new AssignmentContext(_ctx, getState());
		enterRule(_localctx, 24, RULE_assignment);
		int _la;
		try {
			setState(220);
			_errHandler.sync(this);
			switch ( getInterpreter().adaptivePredict(_input,14,_ctx) ) {
			case 1:
				_localctx = new SimpleAssignmentContext(_localctx);
				enterOuterAlt(_localctx, 1);
				{
				setState(199);
				match(ID);
				setState(200);
				assignOp();
				setState(201);
				expression();
				}
				break;
			case 2:
				_localctx = new ArrayAssignmentContext(_localctx);
				enterOuterAlt(_localctx, 2);
				{
				setState(203);
				match(ID);
				setState(208); 
				_errHandler.sync(this);
				_la = _input.LA(1);
				do {
					{
					{
					setState(204);
					match(T__6);
					setState(205);
					expression();
					setState(206);
					match(T__7);
					}
					}
					setState(210); 
					_errHandler.sync(this);
					_la = _input.LA(1);
				} while ( _la==T__6 );
				setState(212);
				assignOp();
				setState(213);
				expression();
				}
				break;
			case 3:
				_localctx = new PointerAssignmentContext(_localctx);
				enterOuterAlt(_localctx, 3);
				{
				setState(215);
				match(T__5);
				setState(216);
				match(ID);
				setState(217);
				assignOp();
				setState(218);
				expression();
				}
				break;
			}
		}
		catch (RecognitionException re) {
			_localctx.exception = re;
			_errHandler.reportError(this, re);
			_errHandler.recover(this, re);
		}
		finally {
			exitRule();
		}
		return _localctx;
	}

	@SuppressWarnings("CheckReturnValue")
	public static class AssignOpContext extends ParserRuleContext {
		public AssignOpContext(ParserRuleContext parent, int invokingState) {
			super(parent, invokingState);
		}
		@Override public int getRuleIndex() { return RULE_assignOp; }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterAssignOp(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitAssignOp(this);
		}
	}

	public final AssignOpContext assignOp() throws RecognitionException {
		AssignOpContext _localctx = new AssignOpContext(_ctx, getState());
		enterRule(_localctx, 26, RULE_assignOp);
		int _la;
		try {
			enterOuterAlt(_localctx, 1);
			{
			setState(222);
			_la = _input.LA(1);
			if ( !((((_la) & ~0x3f) == 0 && ((1L << _la) & 7682L) != 0)) ) {
			_errHandler.recoverInline(this);
			}
			else {
				if ( _input.LA(1)==Token.EOF ) matchedEOF = true;
				_errHandler.reportMatch(this);
				consume();
			}
			}
		}
		catch (RecognitionException re) {
			_localctx.exception = re;
			_errHandler.reportError(this, re);
			_errHandler.recover(this, re);
		}
		finally {
			exitRule();
		}
		return _localctx;
	}

	@SuppressWarnings("CheckReturnValue")
	public static class IncDecStatementContext extends ParserRuleContext {
		public IncDecStatementContext(ParserRuleContext parent, int invokingState) {
			super(parent, invokingState);
		}
		@Override public int getRuleIndex() { return RULE_incDecStatement; }
	 
		public IncDecStatementContext() { }
		public void copyFrom(IncDecStatementContext ctx) {
			super.copyFrom(ctx);
		}
	}
	@SuppressWarnings("CheckReturnValue")
	public static class DecrementStatementContext extends IncDecStatementContext {
		public TerminalNode ID() { return getToken(GolampiParser.ID, 0); }
		public DecrementStatementContext(IncDecStatementContext ctx) { copyFrom(ctx); }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterDecrementStatement(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitDecrementStatement(this);
		}
	}
	@SuppressWarnings("CheckReturnValue")
	public static class IncrementStatementContext extends IncDecStatementContext {
		public TerminalNode ID() { return getToken(GolampiParser.ID, 0); }
		public IncrementStatementContext(IncDecStatementContext ctx) { copyFrom(ctx); }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterIncrementStatement(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitIncrementStatement(this);
		}
	}

	public final IncDecStatementContext incDecStatement() throws RecognitionException {
		IncDecStatementContext _localctx = new IncDecStatementContext(_ctx, getState());
		enterRule(_localctx, 28, RULE_incDecStatement);
		try {
			setState(228);
			_errHandler.sync(this);
			switch ( getInterpreter().adaptivePredict(_input,15,_ctx) ) {
			case 1:
				_localctx = new IncrementStatementContext(_localctx);
				enterOuterAlt(_localctx, 1);
				{
				setState(224);
				match(ID);
				setState(225);
				match(T__12);
				}
				break;
			case 2:
				_localctx = new DecrementStatementContext(_localctx);
				enterOuterAlt(_localctx, 2);
				{
				setState(226);
				match(ID);
				setState(227);
				match(T__13);
				}
				break;
			}
		}
		catch (RecognitionException re) {
			_localctx.exception = re;
			_errHandler.reportError(this, re);
			_errHandler.recover(this, re);
		}
		finally {
			exitRule();
		}
		return _localctx;
	}

	@SuppressWarnings("CheckReturnValue")
	public static class IfStatementContext extends ParserRuleContext {
		public IfStatementContext(ParserRuleContext parent, int invokingState) {
			super(parent, invokingState);
		}
		@Override public int getRuleIndex() { return RULE_ifStatement; }
	 
		public IfStatementContext() { }
		public void copyFrom(IfStatementContext ctx) {
			super.copyFrom(ctx);
		}
	}
	@SuppressWarnings("CheckReturnValue")
	public static class IfElseIfElseContext extends IfStatementContext {
		public List<TerminalNode> IF() { return getTokens(GolampiParser.IF); }
		public TerminalNode IF(int i) {
			return getToken(GolampiParser.IF, i);
		}
		public List<ExpressionContext> expression() {
			return getRuleContexts(ExpressionContext.class);
		}
		public ExpressionContext expression(int i) {
			return getRuleContext(ExpressionContext.class,i);
		}
		public List<BlockContext> block() {
			return getRuleContexts(BlockContext.class);
		}
		public BlockContext block(int i) {
			return getRuleContext(BlockContext.class,i);
		}
		public List<TerminalNode> ELSE() { return getTokens(GolampiParser.ELSE); }
		public TerminalNode ELSE(int i) {
			return getToken(GolampiParser.ELSE, i);
		}
		public IfElseIfElseContext(IfStatementContext ctx) { copyFrom(ctx); }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterIfElseIfElse(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitIfElseIfElse(this);
		}
	}

	public final IfStatementContext ifStatement() throws RecognitionException {
		IfStatementContext _localctx = new IfStatementContext(_ctx, getState());
		enterRule(_localctx, 30, RULE_ifStatement);
		int _la;
		try {
			int _alt;
			_localctx = new IfElseIfElseContext(_localctx);
			enterOuterAlt(_localctx, 1);
			{
			setState(230);
			match(IF);
			setState(231);
			expression();
			setState(232);
			block();
			setState(240);
			_errHandler.sync(this);
			_alt = getInterpreter().adaptivePredict(_input,16,_ctx);
			while ( _alt!=2 && _alt!=org.antlr.v4.runtime.atn.ATN.INVALID_ALT_NUMBER ) {
				if ( _alt==1 ) {
					{
					{
					setState(233);
					match(ELSE);
					setState(234);
					match(IF);
					setState(235);
					expression();
					setState(236);
					block();
					}
					} 
				}
				setState(242);
				_errHandler.sync(this);
				_alt = getInterpreter().adaptivePredict(_input,16,_ctx);
			}
			setState(245);
			_errHandler.sync(this);
			_la = _input.LA(1);
			if (_la==ELSE) {
				{
				setState(243);
				match(ELSE);
				setState(244);
				block();
				}
			}

			}
		}
		catch (RecognitionException re) {
			_localctx.exception = re;
			_errHandler.reportError(this, re);
			_errHandler.recover(this, re);
		}
		finally {
			exitRule();
		}
		return _localctx;
	}

	@SuppressWarnings("CheckReturnValue")
	public static class SwitchStatementContext extends ParserRuleContext {
		public TerminalNode SWITCH() { return getToken(GolampiParser.SWITCH, 0); }
		public ExpressionContext expression() {
			return getRuleContext(ExpressionContext.class,0);
		}
		public List<CaseClauseContext> caseClause() {
			return getRuleContexts(CaseClauseContext.class);
		}
		public CaseClauseContext caseClause(int i) {
			return getRuleContext(CaseClauseContext.class,i);
		}
		public DefaultClauseContext defaultClause() {
			return getRuleContext(DefaultClauseContext.class,0);
		}
		public SwitchStatementContext(ParserRuleContext parent, int invokingState) {
			super(parent, invokingState);
		}
		@Override public int getRuleIndex() { return RULE_switchStatement; }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterSwitchStatement(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitSwitchStatement(this);
		}
	}

	public final SwitchStatementContext switchStatement() throws RecognitionException {
		SwitchStatementContext _localctx = new SwitchStatementContext(_ctx, getState());
		enterRule(_localctx, 32, RULE_switchStatement);
		int _la;
		try {
			enterOuterAlt(_localctx, 1);
			{
			setState(247);
			match(SWITCH);
			setState(248);
			expression();
			setState(249);
			match(T__14);
			setState(253);
			_errHandler.sync(this);
			_la = _input.LA(1);
			while (_la==CASE) {
				{
				{
				setState(250);
				caseClause();
				}
				}
				setState(255);
				_errHandler.sync(this);
				_la = _input.LA(1);
			}
			setState(257);
			_errHandler.sync(this);
			_la = _input.LA(1);
			if (_la==DEFAULT) {
				{
				setState(256);
				defaultClause();
				}
			}

			setState(259);
			match(T__15);
			}
		}
		catch (RecognitionException re) {
			_localctx.exception = re;
			_errHandler.reportError(this, re);
			_errHandler.recover(this, re);
		}
		finally {
			exitRule();
		}
		return _localctx;
	}

	@SuppressWarnings("CheckReturnValue")
	public static class CaseClauseContext extends ParserRuleContext {
		public TerminalNode CASE() { return getToken(GolampiParser.CASE, 0); }
		public ExpressionListContext expressionList() {
			return getRuleContext(ExpressionListContext.class,0);
		}
		public List<StatementContext> statement() {
			return getRuleContexts(StatementContext.class);
		}
		public StatementContext statement(int i) {
			return getRuleContext(StatementContext.class,i);
		}
		public CaseClauseContext(ParserRuleContext parent, int invokingState) {
			super(parent, invokingState);
		}
		@Override public int getRuleIndex() { return RULE_caseClause; }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterCaseClause(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitCaseClause(this);
		}
	}

	public final CaseClauseContext caseClause() throws RecognitionException {
		CaseClauseContext _localctx = new CaseClauseContext(_ctx, getState());
		enterRule(_localctx, 34, RULE_caseClause);
		int _la;
		try {
			enterOuterAlt(_localctx, 1);
			{
			setState(261);
			match(CASE);
			setState(262);
			expressionList();
			setState(263);
			match(T__16);
			setState(267);
			_errHandler.sync(this);
			_la = _input.LA(1);
			while ((((_la) & ~0x3f) == 0 && ((1L << _la) & 558586165247115464L) != 0)) {
				{
				{
				setState(264);
				statement();
				}
				}
				setState(269);
				_errHandler.sync(this);
				_la = _input.LA(1);
			}
			}
		}
		catch (RecognitionException re) {
			_localctx.exception = re;
			_errHandler.reportError(this, re);
			_errHandler.recover(this, re);
		}
		finally {
			exitRule();
		}
		return _localctx;
	}

	@SuppressWarnings("CheckReturnValue")
	public static class DefaultClauseContext extends ParserRuleContext {
		public TerminalNode DEFAULT() { return getToken(GolampiParser.DEFAULT, 0); }
		public List<StatementContext> statement() {
			return getRuleContexts(StatementContext.class);
		}
		public StatementContext statement(int i) {
			return getRuleContext(StatementContext.class,i);
		}
		public DefaultClauseContext(ParserRuleContext parent, int invokingState) {
			super(parent, invokingState);
		}
		@Override public int getRuleIndex() { return RULE_defaultClause; }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterDefaultClause(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitDefaultClause(this);
		}
	}

	public final DefaultClauseContext defaultClause() throws RecognitionException {
		DefaultClauseContext _localctx = new DefaultClauseContext(_ctx, getState());
		enterRule(_localctx, 36, RULE_defaultClause);
		int _la;
		try {
			enterOuterAlt(_localctx, 1);
			{
			setState(270);
			match(DEFAULT);
			setState(271);
			match(T__16);
			setState(275);
			_errHandler.sync(this);
			_la = _input.LA(1);
			while ((((_la) & ~0x3f) == 0 && ((1L << _la) & 558586165247115464L) != 0)) {
				{
				{
				setState(272);
				statement();
				}
				}
				setState(277);
				_errHandler.sync(this);
				_la = _input.LA(1);
			}
			}
		}
		catch (RecognitionException re) {
			_localctx.exception = re;
			_errHandler.reportError(this, re);
			_errHandler.recover(this, re);
		}
		finally {
			exitRule();
		}
		return _localctx;
	}

	@SuppressWarnings("CheckReturnValue")
	public static class ForStatementContext extends ParserRuleContext {
		public ForStatementContext(ParserRuleContext parent, int invokingState) {
			super(parent, invokingState);
		}
		@Override public int getRuleIndex() { return RULE_forStatement; }
	 
		public ForStatementContext() { }
		public void copyFrom(ForStatementContext ctx) {
			super.copyFrom(ctx);
		}
	}
	@SuppressWarnings("CheckReturnValue")
	public static class ForTraditionalContext extends ForStatementContext {
		public TerminalNode FOR() { return getToken(GolampiParser.FOR, 0); }
		public ForClauseContext forClause() {
			return getRuleContext(ForClauseContext.class,0);
		}
		public BlockContext block() {
			return getRuleContext(BlockContext.class,0);
		}
		public ForTraditionalContext(ForStatementContext ctx) { copyFrom(ctx); }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterForTraditional(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitForTraditional(this);
		}
	}
	@SuppressWarnings("CheckReturnValue")
	public static class ForWhileContext extends ForStatementContext {
		public TerminalNode FOR() { return getToken(GolampiParser.FOR, 0); }
		public ExpressionContext expression() {
			return getRuleContext(ExpressionContext.class,0);
		}
		public BlockContext block() {
			return getRuleContext(BlockContext.class,0);
		}
		public ForWhileContext(ForStatementContext ctx) { copyFrom(ctx); }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterForWhile(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitForWhile(this);
		}
	}
	@SuppressWarnings("CheckReturnValue")
	public static class ForInfiniteContext extends ForStatementContext {
		public TerminalNode FOR() { return getToken(GolampiParser.FOR, 0); }
		public BlockContext block() {
			return getRuleContext(BlockContext.class,0);
		}
		public ForInfiniteContext(ForStatementContext ctx) { copyFrom(ctx); }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterForInfinite(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitForInfinite(this);
		}
	}

	public final ForStatementContext forStatement() throws RecognitionException {
		ForStatementContext _localctx = new ForStatementContext(_ctx, getState());
		enterRule(_localctx, 38, RULE_forStatement);
		try {
			setState(288);
			_errHandler.sync(this);
			switch ( getInterpreter().adaptivePredict(_input,22,_ctx) ) {
			case 1:
				_localctx = new ForTraditionalContext(_localctx);
				enterOuterAlt(_localctx, 1);
				{
				setState(278);
				match(FOR);
				setState(279);
				forClause();
				setState(280);
				block();
				}
				break;
			case 2:
				_localctx = new ForWhileContext(_localctx);
				enterOuterAlt(_localctx, 2);
				{
				setState(282);
				match(FOR);
				setState(283);
				expression();
				setState(284);
				block();
				}
				break;
			case 3:
				_localctx = new ForInfiniteContext(_localctx);
				enterOuterAlt(_localctx, 3);
				{
				setState(286);
				match(FOR);
				setState(287);
				block();
				}
				break;
			}
		}
		catch (RecognitionException re) {
			_localctx.exception = re;
			_errHandler.reportError(this, re);
			_errHandler.recover(this, re);
		}
		finally {
			exitRule();
		}
		return _localctx;
	}

	@SuppressWarnings("CheckReturnValue")
	public static class ForClauseContext extends ParserRuleContext {
		public ForInitContext forInit() {
			return getRuleContext(ForInitContext.class,0);
		}
		public ForPostContext forPost() {
			return getRuleContext(ForPostContext.class,0);
		}
		public ExpressionContext expression() {
			return getRuleContext(ExpressionContext.class,0);
		}
		public ForClauseContext(ParserRuleContext parent, int invokingState) {
			super(parent, invokingState);
		}
		@Override public int getRuleIndex() { return RULE_forClause; }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterForClause(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitForClause(this);
		}
	}

	public final ForClauseContext forClause() throws RecognitionException {
		ForClauseContext _localctx = new ForClauseContext(_ctx, getState());
		enterRule(_localctx, 40, RULE_forClause);
		int _la;
		try {
			enterOuterAlt(_localctx, 1);
			{
			setState(290);
			forInit();
			setState(291);
			match(T__17);
			setState(293);
			_errHandler.sync(this);
			_la = _input.LA(1);
			if ((((_la) & ~0x3f) == 0 && ((1L << _la) & 558569500774006984L) != 0)) {
				{
				setState(292);
				expression();
				}
			}

			setState(295);
			match(T__17);
			setState(296);
			forPost();
			}
		}
		catch (RecognitionException re) {
			_localctx.exception = re;
			_errHandler.reportError(this, re);
			_errHandler.recover(this, re);
		}
		finally {
			exitRule();
		}
		return _localctx;
	}

	@SuppressWarnings("CheckReturnValue")
	public static class ForInitContext extends ParserRuleContext {
		public VarDeclarationContext varDeclaration() {
			return getRuleContext(VarDeclarationContext.class,0);
		}
		public ShortVarDeclarationContext shortVarDeclaration() {
			return getRuleContext(ShortVarDeclarationContext.class,0);
		}
		public AssignmentContext assignment() {
			return getRuleContext(AssignmentContext.class,0);
		}
		public IncDecStatementContext incDecStatement() {
			return getRuleContext(IncDecStatementContext.class,0);
		}
		public ForInitContext(ParserRuleContext parent, int invokingState) {
			super(parent, invokingState);
		}
		@Override public int getRuleIndex() { return RULE_forInit; }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterForInit(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitForInit(this);
		}
	}

	public final ForInitContext forInit() throws RecognitionException {
		ForInitContext _localctx = new ForInitContext(_ctx, getState());
		enterRule(_localctx, 42, RULE_forInit);
		try {
			setState(303);
			_errHandler.sync(this);
			switch ( getInterpreter().adaptivePredict(_input,24,_ctx) ) {
			case 1:
				enterOuterAlt(_localctx, 1);
				{
				setState(298);
				varDeclaration();
				}
				break;
			case 2:
				enterOuterAlt(_localctx, 2);
				{
				setState(299);
				shortVarDeclaration();
				}
				break;
			case 3:
				enterOuterAlt(_localctx, 3);
				{
				setState(300);
				assignment();
				}
				break;
			case 4:
				enterOuterAlt(_localctx, 4);
				{
				setState(301);
				incDecStatement();
				}
				break;
			case 5:
				enterOuterAlt(_localctx, 5);
				{
				}
				break;
			}
		}
		catch (RecognitionException re) {
			_localctx.exception = re;
			_errHandler.reportError(this, re);
			_errHandler.recover(this, re);
		}
		finally {
			exitRule();
		}
		return _localctx;
	}

	@SuppressWarnings("CheckReturnValue")
	public static class ForPostContext extends ParserRuleContext {
		public AssignmentContext assignment() {
			return getRuleContext(AssignmentContext.class,0);
		}
		public IncDecStatementContext incDecStatement() {
			return getRuleContext(IncDecStatementContext.class,0);
		}
		public ForPostContext(ParserRuleContext parent, int invokingState) {
			super(parent, invokingState);
		}
		@Override public int getRuleIndex() { return RULE_forPost; }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterForPost(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitForPost(this);
		}
	}

	public final ForPostContext forPost() throws RecognitionException {
		ForPostContext _localctx = new ForPostContext(_ctx, getState());
		enterRule(_localctx, 44, RULE_forPost);
		try {
			setState(308);
			_errHandler.sync(this);
			switch ( getInterpreter().adaptivePredict(_input,25,_ctx) ) {
			case 1:
				enterOuterAlt(_localctx, 1);
				{
				setState(305);
				assignment();
				}
				break;
			case 2:
				enterOuterAlt(_localctx, 2);
				{
				setState(306);
				incDecStatement();
				}
				break;
			case 3:
				enterOuterAlt(_localctx, 3);
				{
				}
				break;
			}
		}
		catch (RecognitionException re) {
			_localctx.exception = re;
			_errHandler.reportError(this, re);
			_errHandler.recover(this, re);
		}
		finally {
			exitRule();
		}
		return _localctx;
	}

	@SuppressWarnings("CheckReturnValue")
	public static class BreakStatementContext extends ParserRuleContext {
		public TerminalNode BREAK() { return getToken(GolampiParser.BREAK, 0); }
		public BreakStatementContext(ParserRuleContext parent, int invokingState) {
			super(parent, invokingState);
		}
		@Override public int getRuleIndex() { return RULE_breakStatement; }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterBreakStatement(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitBreakStatement(this);
		}
	}

	public final BreakStatementContext breakStatement() throws RecognitionException {
		BreakStatementContext _localctx = new BreakStatementContext(_ctx, getState());
		enterRule(_localctx, 46, RULE_breakStatement);
		try {
			enterOuterAlt(_localctx, 1);
			{
			setState(310);
			match(BREAK);
			}
		}
		catch (RecognitionException re) {
			_localctx.exception = re;
			_errHandler.reportError(this, re);
			_errHandler.recover(this, re);
		}
		finally {
			exitRule();
		}
		return _localctx;
	}

	@SuppressWarnings("CheckReturnValue")
	public static class ContinueStatementContext extends ParserRuleContext {
		public TerminalNode CONTINUE() { return getToken(GolampiParser.CONTINUE, 0); }
		public ContinueStatementContext(ParserRuleContext parent, int invokingState) {
			super(parent, invokingState);
		}
		@Override public int getRuleIndex() { return RULE_continueStatement; }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterContinueStatement(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitContinueStatement(this);
		}
	}

	public final ContinueStatementContext continueStatement() throws RecognitionException {
		ContinueStatementContext _localctx = new ContinueStatementContext(_ctx, getState());
		enterRule(_localctx, 48, RULE_continueStatement);
		try {
			enterOuterAlt(_localctx, 1);
			{
			setState(312);
			match(CONTINUE);
			}
		}
		catch (RecognitionException re) {
			_localctx.exception = re;
			_errHandler.reportError(this, re);
			_errHandler.recover(this, re);
		}
		finally {
			exitRule();
		}
		return _localctx;
	}

	@SuppressWarnings("CheckReturnValue")
	public static class ReturnStatementContext extends ParserRuleContext {
		public TerminalNode RETURN() { return getToken(GolampiParser.RETURN, 0); }
		public ExpressionListContext expressionList() {
			return getRuleContext(ExpressionListContext.class,0);
		}
		public ReturnStatementContext(ParserRuleContext parent, int invokingState) {
			super(parent, invokingState);
		}
		@Override public int getRuleIndex() { return RULE_returnStatement; }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterReturnStatement(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitReturnStatement(this);
		}
	}

	public final ReturnStatementContext returnStatement() throws RecognitionException {
		ReturnStatementContext _localctx = new ReturnStatementContext(_ctx, getState());
		enterRule(_localctx, 50, RULE_returnStatement);
		try {
			enterOuterAlt(_localctx, 1);
			{
			setState(314);
			match(RETURN);
			setState(316);
			_errHandler.sync(this);
			switch ( getInterpreter().adaptivePredict(_input,26,_ctx) ) {
			case 1:
				{
				setState(315);
				expressionList();
				}
				break;
			}
			}
		}
		catch (RecognitionException re) {
			_localctx.exception = re;
			_errHandler.reportError(this, re);
			_errHandler.recover(this, re);
		}
		finally {
			exitRule();
		}
		return _localctx;
	}

	@SuppressWarnings("CheckReturnValue")
	public static class BlockContext extends ParserRuleContext {
		public List<DeclarationContext> declaration() {
			return getRuleContexts(DeclarationContext.class);
		}
		public DeclarationContext declaration(int i) {
			return getRuleContext(DeclarationContext.class,i);
		}
		public BlockContext(ParserRuleContext parent, int invokingState) {
			super(parent, invokingState);
		}
		@Override public int getRuleIndex() { return RULE_block; }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterBlock(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitBlock(this);
		}
	}

	public final BlockContext block() throws RecognitionException {
		BlockContext _localctx = new BlockContext(_ctx, getState());
		enterRule(_localctx, 52, RULE_block);
		int _la;
		try {
			enterOuterAlt(_localctx, 1);
			{
			setState(318);
			match(T__14);
			setState(322);
			_errHandler.sync(this);
			_la = _input.LA(1);
			while ((((_la) & ~0x3f) == 0 && ((1L << _la) & 558586195311886536L) != 0)) {
				{
				{
				setState(319);
				declaration();
				}
				}
				setState(324);
				_errHandler.sync(this);
				_la = _input.LA(1);
			}
			setState(325);
			match(T__15);
			}
		}
		catch (RecognitionException re) {
			_localctx.exception = re;
			_errHandler.reportError(this, re);
			_errHandler.recover(this, re);
		}
		finally {
			exitRule();
		}
		return _localctx;
	}

	@SuppressWarnings("CheckReturnValue")
	public static class ExpressionStatementContext extends ParserRuleContext {
		public ExpressionContext expression() {
			return getRuleContext(ExpressionContext.class,0);
		}
		public ExpressionStatementContext(ParserRuleContext parent, int invokingState) {
			super(parent, invokingState);
		}
		@Override public int getRuleIndex() { return RULE_expressionStatement; }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterExpressionStatement(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitExpressionStatement(this);
		}
	}

	public final ExpressionStatementContext expressionStatement() throws RecognitionException {
		ExpressionStatementContext _localctx = new ExpressionStatementContext(_ctx, getState());
		enterRule(_localctx, 54, RULE_expressionStatement);
		try {
			enterOuterAlt(_localctx, 1);
			{
			setState(327);
			expression();
			}
		}
		catch (RecognitionException re) {
			_localctx.exception = re;
			_errHandler.reportError(this, re);
			_errHandler.recover(this, re);
		}
		finally {
			exitRule();
		}
		return _localctx;
	}

	@SuppressWarnings("CheckReturnValue")
	public static class ExpressionContext extends ParserRuleContext {
		public LogicalOrContext logicalOr() {
			return getRuleContext(LogicalOrContext.class,0);
		}
		public ExpressionContext(ParserRuleContext parent, int invokingState) {
			super(parent, invokingState);
		}
		@Override public int getRuleIndex() { return RULE_expression; }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterExpression(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitExpression(this);
		}
	}

	public final ExpressionContext expression() throws RecognitionException {
		ExpressionContext _localctx = new ExpressionContext(_ctx, getState());
		enterRule(_localctx, 56, RULE_expression);
		try {
			enterOuterAlt(_localctx, 1);
			{
			setState(329);
			logicalOr();
			}
		}
		catch (RecognitionException re) {
			_localctx.exception = re;
			_errHandler.reportError(this, re);
			_errHandler.recover(this, re);
		}
		finally {
			exitRule();
		}
		return _localctx;
	}

	@SuppressWarnings("CheckReturnValue")
	public static class LogicalOrContext extends ParserRuleContext {
		public List<LogicalAndContext> logicalAnd() {
			return getRuleContexts(LogicalAndContext.class);
		}
		public LogicalAndContext logicalAnd(int i) {
			return getRuleContext(LogicalAndContext.class,i);
		}
		public List<TerminalNode> OR() { return getTokens(GolampiParser.OR); }
		public TerminalNode OR(int i) {
			return getToken(GolampiParser.OR, i);
		}
		public LogicalOrContext(ParserRuleContext parent, int invokingState) {
			super(parent, invokingState);
		}
		@Override public int getRuleIndex() { return RULE_logicalOr; }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterLogicalOr(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitLogicalOr(this);
		}
	}

	public final LogicalOrContext logicalOr() throws RecognitionException {
		LogicalOrContext _localctx = new LogicalOrContext(_ctx, getState());
		enterRule(_localctx, 58, RULE_logicalOr);
		int _la;
		try {
			enterOuterAlt(_localctx, 1);
			{
			setState(331);
			logicalAnd();
			setState(336);
			_errHandler.sync(this);
			_la = _input.LA(1);
			while (_la==OR) {
				{
				{
				setState(332);
				match(OR);
				setState(333);
				logicalAnd();
				}
				}
				setState(338);
				_errHandler.sync(this);
				_la = _input.LA(1);
			}
			}
		}
		catch (RecognitionException re) {
			_localctx.exception = re;
			_errHandler.reportError(this, re);
			_errHandler.recover(this, re);
		}
		finally {
			exitRule();
		}
		return _localctx;
	}

	@SuppressWarnings("CheckReturnValue")
	public static class LogicalAndContext extends ParserRuleContext {
		public List<EqualityContext> equality() {
			return getRuleContexts(EqualityContext.class);
		}
		public EqualityContext equality(int i) {
			return getRuleContext(EqualityContext.class,i);
		}
		public List<TerminalNode> AND() { return getTokens(GolampiParser.AND); }
		public TerminalNode AND(int i) {
			return getToken(GolampiParser.AND, i);
		}
		public LogicalAndContext(ParserRuleContext parent, int invokingState) {
			super(parent, invokingState);
		}
		@Override public int getRuleIndex() { return RULE_logicalAnd; }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterLogicalAnd(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitLogicalAnd(this);
		}
	}

	public final LogicalAndContext logicalAnd() throws RecognitionException {
		LogicalAndContext _localctx = new LogicalAndContext(_ctx, getState());
		enterRule(_localctx, 60, RULE_logicalAnd);
		int _la;
		try {
			enterOuterAlt(_localctx, 1);
			{
			setState(339);
			equality();
			setState(344);
			_errHandler.sync(this);
			_la = _input.LA(1);
			while (_la==AND) {
				{
				{
				setState(340);
				match(AND);
				setState(341);
				equality();
				}
				}
				setState(346);
				_errHandler.sync(this);
				_la = _input.LA(1);
			}
			}
		}
		catch (RecognitionException re) {
			_localctx.exception = re;
			_errHandler.reportError(this, re);
			_errHandler.recover(this, re);
		}
		finally {
			exitRule();
		}
		return _localctx;
	}

	@SuppressWarnings("CheckReturnValue")
	public static class EqualityContext extends ParserRuleContext {
		public List<RelationalContext> relational() {
			return getRuleContexts(RelationalContext.class);
		}
		public RelationalContext relational(int i) {
			return getRuleContext(RelationalContext.class,i);
		}
		public EqualityContext(ParserRuleContext parent, int invokingState) {
			super(parent, invokingState);
		}
		@Override public int getRuleIndex() { return RULE_equality; }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterEquality(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitEquality(this);
		}
	}

	public final EqualityContext equality() throws RecognitionException {
		EqualityContext _localctx = new EqualityContext(_ctx, getState());
		enterRule(_localctx, 62, RULE_equality);
		int _la;
		try {
			enterOuterAlt(_localctx, 1);
			{
			setState(347);
			relational();
			setState(352);
			_errHandler.sync(this);
			_la = _input.LA(1);
			while (_la==T__18 || _la==T__19) {
				{
				{
				setState(348);
				_la = _input.LA(1);
				if ( !(_la==T__18 || _la==T__19) ) {
				_errHandler.recoverInline(this);
				}
				else {
					if ( _input.LA(1)==Token.EOF ) matchedEOF = true;
					_errHandler.reportMatch(this);
					consume();
				}
				setState(349);
				relational();
				}
				}
				setState(354);
				_errHandler.sync(this);
				_la = _input.LA(1);
			}
			}
		}
		catch (RecognitionException re) {
			_localctx.exception = re;
			_errHandler.reportError(this, re);
			_errHandler.recover(this, re);
		}
		finally {
			exitRule();
		}
		return _localctx;
	}

	@SuppressWarnings("CheckReturnValue")
	public static class RelationalContext extends ParserRuleContext {
		public List<AdditiveContext> additive() {
			return getRuleContexts(AdditiveContext.class);
		}
		public AdditiveContext additive(int i) {
			return getRuleContext(AdditiveContext.class,i);
		}
		public RelationalContext(ParserRuleContext parent, int invokingState) {
			super(parent, invokingState);
		}
		@Override public int getRuleIndex() { return RULE_relational; }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterRelational(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitRelational(this);
		}
	}

	public final RelationalContext relational() throws RecognitionException {
		RelationalContext _localctx = new RelationalContext(_ctx, getState());
		enterRule(_localctx, 64, RULE_relational);
		int _la;
		try {
			enterOuterAlt(_localctx, 1);
			{
			setState(355);
			additive();
			setState(360);
			_errHandler.sync(this);
			_la = _input.LA(1);
			while ((((_la) & ~0x3f) == 0 && ((1L << _la) & 31457280L) != 0)) {
				{
				{
				setState(356);
				_la = _input.LA(1);
				if ( !((((_la) & ~0x3f) == 0 && ((1L << _la) & 31457280L) != 0)) ) {
				_errHandler.recoverInline(this);
				}
				else {
					if ( _input.LA(1)==Token.EOF ) matchedEOF = true;
					_errHandler.reportMatch(this);
					consume();
				}
				setState(357);
				additive();
				}
				}
				setState(362);
				_errHandler.sync(this);
				_la = _input.LA(1);
			}
			}
		}
		catch (RecognitionException re) {
			_localctx.exception = re;
			_errHandler.reportError(this, re);
			_errHandler.recover(this, re);
		}
		finally {
			exitRule();
		}
		return _localctx;
	}

	@SuppressWarnings("CheckReturnValue")
	public static class AdditiveContext extends ParserRuleContext {
		public List<MultiplicativeContext> multiplicative() {
			return getRuleContexts(MultiplicativeContext.class);
		}
		public MultiplicativeContext multiplicative(int i) {
			return getRuleContext(MultiplicativeContext.class,i);
		}
		public AdditiveContext(ParserRuleContext parent, int invokingState) {
			super(parent, invokingState);
		}
		@Override public int getRuleIndex() { return RULE_additive; }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterAdditive(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitAdditive(this);
		}
	}

	public final AdditiveContext additive() throws RecognitionException {
		AdditiveContext _localctx = new AdditiveContext(_ctx, getState());
		enterRule(_localctx, 66, RULE_additive);
		int _la;
		try {
			int _alt;
			enterOuterAlt(_localctx, 1);
			{
			setState(363);
			multiplicative();
			setState(368);
			_errHandler.sync(this);
			_alt = getInterpreter().adaptivePredict(_input,32,_ctx);
			while ( _alt!=2 && _alt!=org.antlr.v4.runtime.atn.ATN.INVALID_ALT_NUMBER ) {
				if ( _alt==1 ) {
					{
					{
					setState(364);
					_la = _input.LA(1);
					if ( !(_la==T__24 || _la==T__25) ) {
					_errHandler.recoverInline(this);
					}
					else {
						if ( _input.LA(1)==Token.EOF ) matchedEOF = true;
						_errHandler.reportMatch(this);
						consume();
					}
					setState(365);
					multiplicative();
					}
					} 
				}
				setState(370);
				_errHandler.sync(this);
				_alt = getInterpreter().adaptivePredict(_input,32,_ctx);
			}
			}
		}
		catch (RecognitionException re) {
			_localctx.exception = re;
			_errHandler.reportError(this, re);
			_errHandler.recover(this, re);
		}
		finally {
			exitRule();
		}
		return _localctx;
	}

	@SuppressWarnings("CheckReturnValue")
	public static class MultiplicativeContext extends ParserRuleContext {
		public List<UnaryContext> unary() {
			return getRuleContexts(UnaryContext.class);
		}
		public UnaryContext unary(int i) {
			return getRuleContext(UnaryContext.class,i);
		}
		public MultiplicativeContext(ParserRuleContext parent, int invokingState) {
			super(parent, invokingState);
		}
		@Override public int getRuleIndex() { return RULE_multiplicative; }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterMultiplicative(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitMultiplicative(this);
		}
	}

	public final MultiplicativeContext multiplicative() throws RecognitionException {
		MultiplicativeContext _localctx = new MultiplicativeContext(_ctx, getState());
		enterRule(_localctx, 68, RULE_multiplicative);
		int _la;
		try {
			int _alt;
			enterOuterAlt(_localctx, 1);
			{
			setState(371);
			unary();
			setState(376);
			_errHandler.sync(this);
			_alt = getInterpreter().adaptivePredict(_input,33,_ctx);
			while ( _alt!=2 && _alt!=org.antlr.v4.runtime.atn.ATN.INVALID_ALT_NUMBER ) {
				if ( _alt==1 ) {
					{
					{
					setState(372);
					_la = _input.LA(1);
					if ( !((((_la) & ~0x3f) == 0 && ((1L << _la) & 402653248L) != 0)) ) {
					_errHandler.recoverInline(this);
					}
					else {
						if ( _input.LA(1)==Token.EOF ) matchedEOF = true;
						_errHandler.reportMatch(this);
						consume();
					}
					setState(373);
					unary();
					}
					} 
				}
				setState(378);
				_errHandler.sync(this);
				_alt = getInterpreter().adaptivePredict(_input,33,_ctx);
			}
			}
		}
		catch (RecognitionException re) {
			_localctx.exception = re;
			_errHandler.reportError(this, re);
			_errHandler.recover(this, re);
		}
		finally {
			exitRule();
		}
		return _localctx;
	}

	@SuppressWarnings("CheckReturnValue")
	public static class UnaryContext extends ParserRuleContext {
		public UnaryContext(ParserRuleContext parent, int invokingState) {
			super(parent, invokingState);
		}
		@Override public int getRuleIndex() { return RULE_unary; }
	 
		public UnaryContext() { }
		public void copyFrom(UnaryContext ctx) {
			super.copyFrom(ctx);
		}
	}
	@SuppressWarnings("CheckReturnValue")
	public static class AddressOfContext extends UnaryContext {
		public TerminalNode ID() { return getToken(GolampiParser.ID, 0); }
		public AddressOfContext(UnaryContext ctx) { copyFrom(ctx); }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterAddressOf(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitAddressOf(this);
		}
	}
	@SuppressWarnings("CheckReturnValue")
	public static class NegativeUnaryContext extends UnaryContext {
		public UnaryContext unary() {
			return getRuleContext(UnaryContext.class,0);
		}
		public NegativeUnaryContext(UnaryContext ctx) { copyFrom(ctx); }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterNegativeUnary(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitNegativeUnary(this);
		}
	}
	@SuppressWarnings("CheckReturnValue")
	public static class NotUnaryContext extends UnaryContext {
		public UnaryContext unary() {
			return getRuleContext(UnaryContext.class,0);
		}
		public NotUnaryContext(UnaryContext ctx) { copyFrom(ctx); }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterNotUnary(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitNotUnary(this);
		}
	}
	@SuppressWarnings("CheckReturnValue")
	public static class DereferenceContext extends UnaryContext {
		public UnaryContext unary() {
			return getRuleContext(UnaryContext.class,0);
		}
		public DereferenceContext(UnaryContext ctx) { copyFrom(ctx); }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterDereference(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitDereference(this);
		}
	}
	@SuppressWarnings("CheckReturnValue")
	public static class PrimaryUnaryContext extends UnaryContext {
		public PrimaryContext primary() {
			return getRuleContext(PrimaryContext.class,0);
		}
		public PrimaryUnaryContext(UnaryContext ctx) { copyFrom(ctx); }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterPrimaryUnary(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitPrimaryUnary(this);
		}
	}

	public final UnaryContext unary() throws RecognitionException {
		UnaryContext _localctx = new UnaryContext(_ctx, getState());
		enterRule(_localctx, 70, RULE_unary);
		try {
			setState(388);
			_errHandler.sync(this);
			switch (_input.LA(1)) {
			case T__2:
			case T__6:
			case T__14:
			case TRUE:
			case FALSE:
			case NIL:
			case INT32:
			case FLOAT32:
			case RUNE:
			case STRING:
			case ID:
				_localctx = new PrimaryUnaryContext(_localctx);
				enterOuterAlt(_localctx, 1);
				{
				setState(379);
				primary();
				}
				break;
			case T__25:
				_localctx = new NegativeUnaryContext(_localctx);
				enterOuterAlt(_localctx, 2);
				{
				setState(380);
				match(T__25);
				setState(381);
				unary();
				}
				break;
			case T__28:
				_localctx = new NotUnaryContext(_localctx);
				enterOuterAlt(_localctx, 3);
				{
				setState(382);
				match(T__28);
				setState(383);
				unary();
				}
				break;
			case T__29:
				_localctx = new AddressOfContext(_localctx);
				enterOuterAlt(_localctx, 4);
				{
				setState(384);
				match(T__29);
				setState(385);
				match(ID);
				}
				break;
			case T__5:
				_localctx = new DereferenceContext(_localctx);
				enterOuterAlt(_localctx, 5);
				{
				setState(386);
				match(T__5);
				setState(387);
				unary();
				}
				break;
			default:
				throw new NoViableAltException(this);
			}
		}
		catch (RecognitionException re) {
			_localctx.exception = re;
			_errHandler.reportError(this, re);
			_errHandler.recover(this, re);
		}
		finally {
			exitRule();
		}
		return _localctx;
	}

	@SuppressWarnings("CheckReturnValue")
	public static class PrimaryContext extends ParserRuleContext {
		public PrimaryContext(ParserRuleContext parent, int invokingState) {
			super(parent, invokingState);
		}
		@Override public int getRuleIndex() { return RULE_primary; }
	 
		public PrimaryContext() { }
		public void copyFrom(PrimaryContext ctx) {
			super.copyFrom(ctx);
		}
	}
	@SuppressWarnings("CheckReturnValue")
	public static class FloatLiteralContext extends PrimaryContext {
		public TerminalNode FLOAT32() { return getToken(GolampiParser.FLOAT32, 0); }
		public FloatLiteralContext(PrimaryContext ctx) { copyFrom(ctx); }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterFloatLiteral(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitFloatLiteral(this);
		}
	}
	@SuppressWarnings("CheckReturnValue")
	public static class GroupedExpressionContext extends PrimaryContext {
		public ExpressionContext expression() {
			return getRuleContext(ExpressionContext.class,0);
		}
		public GroupedExpressionContext(PrimaryContext ctx) { copyFrom(ctx); }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterGroupedExpression(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitGroupedExpression(this);
		}
	}
	@SuppressWarnings("CheckReturnValue")
	public static class FalseLiteralContext extends PrimaryContext {
		public TerminalNode FALSE() { return getToken(GolampiParser.FALSE, 0); }
		public FalseLiteralContext(PrimaryContext ctx) { copyFrom(ctx); }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterFalseLiteral(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitFalseLiteral(this);
		}
	}
	@SuppressWarnings("CheckReturnValue")
	public static class ArrayAccessContext extends PrimaryContext {
		public TerminalNode ID() { return getToken(GolampiParser.ID, 0); }
		public List<ExpressionContext> expression() {
			return getRuleContexts(ExpressionContext.class);
		}
		public ExpressionContext expression(int i) {
			return getRuleContext(ExpressionContext.class,i);
		}
		public ArrayAccessContext(PrimaryContext ctx) { copyFrom(ctx); }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterArrayAccess(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitArrayAccess(this);
		}
	}
	@SuppressWarnings("CheckReturnValue")
	public static class IdentifierContext extends PrimaryContext {
		public TerminalNode ID() { return getToken(GolampiParser.ID, 0); }
		public IdentifierContext(PrimaryContext ctx) { copyFrom(ctx); }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterIdentifier(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitIdentifier(this);
		}
	}
	@SuppressWarnings("CheckReturnValue")
	public static class StringLiteralContext extends PrimaryContext {
		public TerminalNode STRING() { return getToken(GolampiParser.STRING, 0); }
		public StringLiteralContext(PrimaryContext ctx) { copyFrom(ctx); }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterStringLiteral(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitStringLiteral(this);
		}
	}
	@SuppressWarnings("CheckReturnValue")
	public static class TrueLiteralContext extends PrimaryContext {
		public TerminalNode TRUE() { return getToken(GolampiParser.TRUE, 0); }
		public TrueLiteralContext(PrimaryContext ctx) { copyFrom(ctx); }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterTrueLiteral(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitTrueLiteral(this);
		}
	}
	@SuppressWarnings("CheckReturnValue")
	public static class InnerArrayLiteralContext extends PrimaryContext {
		public ExpressionListContext expressionList() {
			return getRuleContext(ExpressionListContext.class,0);
		}
		public InnerArrayLiteralContext(PrimaryContext ctx) { copyFrom(ctx); }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterInnerArrayLiteral(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitInnerArrayLiteral(this);
		}
	}
	@SuppressWarnings("CheckReturnValue")
	public static class NilLiteralContext extends PrimaryContext {
		public TerminalNode NIL() { return getToken(GolampiParser.NIL, 0); }
		public NilLiteralContext(PrimaryContext ctx) { copyFrom(ctx); }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterNilLiteral(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitNilLiteral(this);
		}
	}
	@SuppressWarnings("CheckReturnValue")
	public static class ArrayLiteralExprContext extends PrimaryContext {
		public ArrayLiteralContext arrayLiteral() {
			return getRuleContext(ArrayLiteralContext.class,0);
		}
		public ArrayLiteralExprContext(PrimaryContext ctx) { copyFrom(ctx); }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterArrayLiteralExpr(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitArrayLiteralExpr(this);
		}
	}
	@SuppressWarnings("CheckReturnValue")
	public static class IntLiteralContext extends PrimaryContext {
		public TerminalNode INT32() { return getToken(GolampiParser.INT32, 0); }
		public IntLiteralContext(PrimaryContext ctx) { copyFrom(ctx); }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterIntLiteral(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitIntLiteral(this);
		}
	}
	@SuppressWarnings("CheckReturnValue")
	public static class FunctionCallContext extends PrimaryContext {
		public List<TerminalNode> ID() { return getTokens(GolampiParser.ID); }
		public TerminalNode ID(int i) {
			return getToken(GolampiParser.ID, i);
		}
		public ArgumentListContext argumentList() {
			return getRuleContext(ArgumentListContext.class,0);
		}
		public FunctionCallContext(PrimaryContext ctx) { copyFrom(ctx); }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterFunctionCall(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitFunctionCall(this);
		}
	}
	@SuppressWarnings("CheckReturnValue")
	public static class RuneLiteralContext extends PrimaryContext {
		public TerminalNode RUNE() { return getToken(GolampiParser.RUNE, 0); }
		public RuneLiteralContext(PrimaryContext ctx) { copyFrom(ctx); }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterRuneLiteral(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitRuneLiteral(this);
		}
	}

	public final PrimaryContext primary() throws RecognitionException {
		PrimaryContext _localctx = new PrimaryContext(_ctx, getState());
		enterRule(_localctx, 72, RULE_primary);
		int _la;
		try {
			int _alt;
			setState(426);
			_errHandler.sync(this);
			switch ( getInterpreter().adaptivePredict(_input,38,_ctx) ) {
			case 1:
				_localctx = new IntLiteralContext(_localctx);
				enterOuterAlt(_localctx, 1);
				{
				setState(390);
				match(INT32);
				}
				break;
			case 2:
				_localctx = new FloatLiteralContext(_localctx);
				enterOuterAlt(_localctx, 2);
				{
				setState(391);
				match(FLOAT32);
				}
				break;
			case 3:
				_localctx = new RuneLiteralContext(_localctx);
				enterOuterAlt(_localctx, 3);
				{
				setState(392);
				match(RUNE);
				}
				break;
			case 4:
				_localctx = new StringLiteralContext(_localctx);
				enterOuterAlt(_localctx, 4);
				{
				setState(393);
				match(STRING);
				}
				break;
			case 5:
				_localctx = new TrueLiteralContext(_localctx);
				enterOuterAlt(_localctx, 5);
				{
				setState(394);
				match(TRUE);
				}
				break;
			case 6:
				_localctx = new FalseLiteralContext(_localctx);
				enterOuterAlt(_localctx, 6);
				{
				setState(395);
				match(FALSE);
				}
				break;
			case 7:
				_localctx = new NilLiteralContext(_localctx);
				enterOuterAlt(_localctx, 7);
				{
				setState(396);
				match(NIL);
				}
				break;
			case 8:
				_localctx = new FunctionCallContext(_localctx);
				enterOuterAlt(_localctx, 8);
				{
				setState(397);
				match(ID);
				setState(400);
				_errHandler.sync(this);
				_la = _input.LA(1);
				if (_la==T__30) {
					{
					setState(398);
					match(T__30);
					setState(399);
					match(ID);
					}
				}

				setState(402);
				match(T__2);
				setState(404);
				_errHandler.sync(this);
				_la = _input.LA(1);
				if ((((_la) & ~0x3f) == 0 && ((1L << _la) & 558569500774006984L) != 0)) {
					{
					setState(403);
					argumentList();
					}
				}

				setState(406);
				match(T__3);
				}
				break;
			case 9:
				_localctx = new ArrayAccessContext(_localctx);
				enterOuterAlt(_localctx, 9);
				{
				setState(407);
				match(ID);
				setState(412); 
				_errHandler.sync(this);
				_alt = 1;
				do {
					switch (_alt) {
					case 1:
						{
						{
						setState(408);
						match(T__6);
						setState(409);
						expression();
						setState(410);
						match(T__7);
						}
						}
						break;
					default:
						throw new NoViableAltException(this);
					}
					setState(414); 
					_errHandler.sync(this);
					_alt = getInterpreter().adaptivePredict(_input,37,_ctx);
				} while ( _alt!=2 && _alt!=org.antlr.v4.runtime.atn.ATN.INVALID_ALT_NUMBER );
				}
				break;
			case 10:
				_localctx = new IdentifierContext(_localctx);
				enterOuterAlt(_localctx, 10);
				{
				setState(416);
				match(ID);
				}
				break;
			case 11:
				_localctx = new GroupedExpressionContext(_localctx);
				enterOuterAlt(_localctx, 11);
				{
				setState(417);
				match(T__2);
				setState(418);
				expression();
				setState(419);
				match(T__3);
				}
				break;
			case 12:
				_localctx = new ArrayLiteralExprContext(_localctx);
				enterOuterAlt(_localctx, 12);
				{
				setState(421);
				arrayLiteral();
				}
				break;
			case 13:
				_localctx = new InnerArrayLiteralContext(_localctx);
				enterOuterAlt(_localctx, 13);
				{
				setState(422);
				match(T__14);
				setState(423);
				expressionList();
				setState(424);
				match(T__15);
				}
				break;
			}
		}
		catch (RecognitionException re) {
			_localctx.exception = re;
			_errHandler.reportError(this, re);
			_errHandler.recover(this, re);
		}
		finally {
			exitRule();
		}
		return _localctx;
	}

	@SuppressWarnings("CheckReturnValue")
	public static class ArrayLiteralContext extends ParserRuleContext {
		public ArrayLiteralContext(ParserRuleContext parent, int invokingState) {
			super(parent, invokingState);
		}
		@Override public int getRuleIndex() { return RULE_arrayLiteral; }
	 
		public ArrayLiteralContext() { }
		public void copyFrom(ArrayLiteralContext ctx) {
			super.copyFrom(ctx);
		}
	}
	@SuppressWarnings("CheckReturnValue")
	public static class FixedArrayLiteralNodeContext extends ArrayLiteralContext {
		public ExpressionContext expression() {
			return getRuleContext(ExpressionContext.class,0);
		}
		public TypeContext type() {
			return getRuleContext(TypeContext.class,0);
		}
		public ExpressionListContext expressionList() {
			return getRuleContext(ExpressionListContext.class,0);
		}
		public InnerLiteralListContext innerLiteralList() {
			return getRuleContext(InnerLiteralListContext.class,0);
		}
		public FixedArrayLiteralNodeContext(ArrayLiteralContext ctx) { copyFrom(ctx); }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterFixedArrayLiteralNode(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitFixedArrayLiteralNode(this);
		}
	}
	@SuppressWarnings("CheckReturnValue")
	public static class SliceLiteralNodeContext extends ArrayLiteralContext {
		public TypeContext type() {
			return getRuleContext(TypeContext.class,0);
		}
		public ExpressionListContext expressionList() {
			return getRuleContext(ExpressionListContext.class,0);
		}
		public SliceLiteralNodeContext(ArrayLiteralContext ctx) { copyFrom(ctx); }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterSliceLiteralNode(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitSliceLiteralNode(this);
		}
	}

	public final ArrayLiteralContext arrayLiteral() throws RecognitionException {
		ArrayLiteralContext _localctx = new ArrayLiteralContext(_ctx, getState());
		enterRule(_localctx, 74, RULE_arrayLiteral);
		int _la;
		try {
			setState(448);
			_errHandler.sync(this);
			switch ( getInterpreter().adaptivePredict(_input,41,_ctx) ) {
			case 1:
				_localctx = new FixedArrayLiteralNodeContext(_localctx);
				enterOuterAlt(_localctx, 1);
				{
				setState(428);
				match(T__6);
				setState(429);
				expression();
				setState(430);
				match(T__7);
				setState(431);
				type();
				setState(432);
				match(T__14);
				setState(435);
				_errHandler.sync(this);
				switch ( getInterpreter().adaptivePredict(_input,39,_ctx) ) {
				case 1:
					{
					setState(433);
					expressionList();
					}
					break;
				case 2:
					{
					setState(434);
					innerLiteralList();
					}
					break;
				}
				setState(437);
				match(T__15);
				}
				break;
			case 2:
				_localctx = new SliceLiteralNodeContext(_localctx);
				enterOuterAlt(_localctx, 2);
				{
				setState(439);
				match(T__6);
				setState(440);
				match(T__7);
				setState(441);
				type();
				setState(442);
				match(T__14);
				setState(444);
				_errHandler.sync(this);
				_la = _input.LA(1);
				if ((((_la) & ~0x3f) == 0 && ((1L << _la) & 558569500774006984L) != 0)) {
					{
					setState(443);
					expressionList();
					}
				}

				setState(446);
				match(T__15);
				}
				break;
			}
		}
		catch (RecognitionException re) {
			_localctx.exception = re;
			_errHandler.reportError(this, re);
			_errHandler.recover(this, re);
		}
		finally {
			exitRule();
		}
		return _localctx;
	}

	@SuppressWarnings("CheckReturnValue")
	public static class InnerLiteralListContext extends ParserRuleContext {
		public List<InnerLiteralContext> innerLiteral() {
			return getRuleContexts(InnerLiteralContext.class);
		}
		public InnerLiteralContext innerLiteral(int i) {
			return getRuleContext(InnerLiteralContext.class,i);
		}
		public InnerLiteralListContext(ParserRuleContext parent, int invokingState) {
			super(parent, invokingState);
		}
		@Override public int getRuleIndex() { return RULE_innerLiteralList; }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterInnerLiteralList(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitInnerLiteralList(this);
		}
	}

	public final InnerLiteralListContext innerLiteralList() throws RecognitionException {
		InnerLiteralListContext _localctx = new InnerLiteralListContext(_ctx, getState());
		enterRule(_localctx, 76, RULE_innerLiteralList);
		int _la;
		try {
			int _alt;
			enterOuterAlt(_localctx, 1);
			{
			setState(450);
			innerLiteral();
			setState(455);
			_errHandler.sync(this);
			_alt = getInterpreter().adaptivePredict(_input,42,_ctx);
			while ( _alt!=2 && _alt!=org.antlr.v4.runtime.atn.ATN.INVALID_ALT_NUMBER ) {
				if ( _alt==1 ) {
					{
					{
					setState(451);
					match(T__4);
					setState(452);
					innerLiteral();
					}
					} 
				}
				setState(457);
				_errHandler.sync(this);
				_alt = getInterpreter().adaptivePredict(_input,42,_ctx);
			}
			setState(459);
			_errHandler.sync(this);
			_la = _input.LA(1);
			if (_la==T__4) {
				{
				setState(458);
				match(T__4);
				}
			}

			}
		}
		catch (RecognitionException re) {
			_localctx.exception = re;
			_errHandler.reportError(this, re);
			_errHandler.recover(this, re);
		}
		finally {
			exitRule();
		}
		return _localctx;
	}

	@SuppressWarnings("CheckReturnValue")
	public static class InnerLiteralContext extends ParserRuleContext {
		public ExpressionListContext expressionList() {
			return getRuleContext(ExpressionListContext.class,0);
		}
		public InnerLiteralContext(ParserRuleContext parent, int invokingState) {
			super(parent, invokingState);
		}
		@Override public int getRuleIndex() { return RULE_innerLiteral; }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterInnerLiteral(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitInnerLiteral(this);
		}
	}

	public final InnerLiteralContext innerLiteral() throws RecognitionException {
		InnerLiteralContext _localctx = new InnerLiteralContext(_ctx, getState());
		enterRule(_localctx, 78, RULE_innerLiteral);
		int _la;
		try {
			enterOuterAlt(_localctx, 1);
			{
			setState(461);
			match(T__14);
			setState(462);
			expressionList();
			setState(464);
			_errHandler.sync(this);
			_la = _input.LA(1);
			if (_la==T__4) {
				{
				setState(463);
				match(T__4);
				}
			}

			setState(466);
			match(T__15);
			}
		}
		catch (RecognitionException re) {
			_localctx.exception = re;
			_errHandler.reportError(this, re);
			_errHandler.recover(this, re);
		}
		finally {
			exitRule();
		}
		return _localctx;
	}

	@SuppressWarnings("CheckReturnValue")
	public static class ArgumentListContext extends ParserRuleContext {
		public List<ArgumentContext> argument() {
			return getRuleContexts(ArgumentContext.class);
		}
		public ArgumentContext argument(int i) {
			return getRuleContext(ArgumentContext.class,i);
		}
		public ArgumentListContext(ParserRuleContext parent, int invokingState) {
			super(parent, invokingState);
		}
		@Override public int getRuleIndex() { return RULE_argumentList; }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterArgumentList(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitArgumentList(this);
		}
	}

	public final ArgumentListContext argumentList() throws RecognitionException {
		ArgumentListContext _localctx = new ArgumentListContext(_ctx, getState());
		enterRule(_localctx, 80, RULE_argumentList);
		int _la;
		try {
			enterOuterAlt(_localctx, 1);
			{
			setState(468);
			argument();
			setState(473);
			_errHandler.sync(this);
			_la = _input.LA(1);
			while (_la==T__4) {
				{
				{
				setState(469);
				match(T__4);
				setState(470);
				argument();
				}
				}
				setState(475);
				_errHandler.sync(this);
				_la = _input.LA(1);
			}
			}
		}
		catch (RecognitionException re) {
			_localctx.exception = re;
			_errHandler.reportError(this, re);
			_errHandler.recover(this, re);
		}
		finally {
			exitRule();
		}
		return _localctx;
	}

	@SuppressWarnings("CheckReturnValue")
	public static class ArgumentContext extends ParserRuleContext {
		public ArgumentContext(ParserRuleContext parent, int invokingState) {
			super(parent, invokingState);
		}
		@Override public int getRuleIndex() { return RULE_argument; }
	 
		public ArgumentContext() { }
		public void copyFrom(ArgumentContext ctx) {
			super.copyFrom(ctx);
		}
	}
	@SuppressWarnings("CheckReturnValue")
	public static class AddressArgumentContext extends ArgumentContext {
		public TerminalNode ID() { return getToken(GolampiParser.ID, 0); }
		public AddressArgumentContext(ArgumentContext ctx) { copyFrom(ctx); }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterAddressArgument(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitAddressArgument(this);
		}
	}
	@SuppressWarnings("CheckReturnValue")
	public static class ExpressionArgumentContext extends ArgumentContext {
		public ExpressionContext expression() {
			return getRuleContext(ExpressionContext.class,0);
		}
		public ExpressionArgumentContext(ArgumentContext ctx) { copyFrom(ctx); }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterExpressionArgument(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitExpressionArgument(this);
		}
	}

	public final ArgumentContext argument() throws RecognitionException {
		ArgumentContext _localctx = new ArgumentContext(_ctx, getState());
		enterRule(_localctx, 82, RULE_argument);
		try {
			setState(479);
			_errHandler.sync(this);
			switch ( getInterpreter().adaptivePredict(_input,46,_ctx) ) {
			case 1:
				_localctx = new ExpressionArgumentContext(_localctx);
				enterOuterAlt(_localctx, 1);
				{
				setState(476);
				expression();
				}
				break;
			case 2:
				_localctx = new AddressArgumentContext(_localctx);
				enterOuterAlt(_localctx, 2);
				{
				setState(477);
				match(T__29);
				setState(478);
				match(ID);
				}
				break;
			}
		}
		catch (RecognitionException re) {
			_localctx.exception = re;
			_errHandler.reportError(this, re);
			_errHandler.recover(this, re);
		}
		finally {
			exitRule();
		}
		return _localctx;
	}

	@SuppressWarnings("CheckReturnValue")
	public static class TypeContext extends ParserRuleContext {
		public TypeContext(ParserRuleContext parent, int invokingState) {
			super(parent, invokingState);
		}
		@Override public int getRuleIndex() { return RULE_type; }
	 
		public TypeContext() { }
		public void copyFrom(TypeContext ctx) {
			super.copyFrom(ctx);
		}
	}
	@SuppressWarnings("CheckReturnValue")
	public static class Float32TypeContext extends TypeContext {
		public TerminalNode FLOAT32_TYPE() { return getToken(GolampiParser.FLOAT32_TYPE, 0); }
		public Float32TypeContext(TypeContext ctx) { copyFrom(ctx); }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterFloat32Type(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitFloat32Type(this);
		}
	}
	@SuppressWarnings("CheckReturnValue")
	public static class ArrayTypeContext extends TypeContext {
		public ExpressionContext expression() {
			return getRuleContext(ExpressionContext.class,0);
		}
		public TypeContext type() {
			return getRuleContext(TypeContext.class,0);
		}
		public ArrayTypeContext(TypeContext ctx) { copyFrom(ctx); }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterArrayType(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitArrayType(this);
		}
	}
	@SuppressWarnings("CheckReturnValue")
	public static class BoolTypeContext extends TypeContext {
		public TerminalNode BOOL_TYPE() { return getToken(GolampiParser.BOOL_TYPE, 0); }
		public BoolTypeContext(TypeContext ctx) { copyFrom(ctx); }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterBoolType(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitBoolType(this);
		}
	}
	@SuppressWarnings("CheckReturnValue")
	public static class RuneTypeContext extends TypeContext {
		public TerminalNode RUNE_TYPE() { return getToken(GolampiParser.RUNE_TYPE, 0); }
		public RuneTypeContext(TypeContext ctx) { copyFrom(ctx); }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterRuneType(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitRuneType(this);
		}
	}
	@SuppressWarnings("CheckReturnValue")
	public static class StringTypeContext extends TypeContext {
		public TerminalNode STRING_TYPE() { return getToken(GolampiParser.STRING_TYPE, 0); }
		public StringTypeContext(TypeContext ctx) { copyFrom(ctx); }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterStringType(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitStringType(this);
		}
	}
	@SuppressWarnings("CheckReturnValue")
	public static class SliceTypeContext extends TypeContext {
		public TypeContext type() {
			return getRuleContext(TypeContext.class,0);
		}
		public SliceTypeContext(TypeContext ctx) { copyFrom(ctx); }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterSliceType(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitSliceType(this);
		}
	}
	@SuppressWarnings("CheckReturnValue")
	public static class PointerTypeContext extends TypeContext {
		public TypeContext type() {
			return getRuleContext(TypeContext.class,0);
		}
		public PointerTypeContext(TypeContext ctx) { copyFrom(ctx); }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterPointerType(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitPointerType(this);
		}
	}
	@SuppressWarnings("CheckReturnValue")
	public static class Int32TypeContext extends TypeContext {
		public TerminalNode INT32_TYPE() { return getToken(GolampiParser.INT32_TYPE, 0); }
		public Int32TypeContext(TypeContext ctx) { copyFrom(ctx); }
		@Override
		public void enterRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).enterInt32Type(this);
		}
		@Override
		public void exitRule(ParseTreeListener listener) {
			if ( listener instanceof GolampiListener ) ((GolampiListener)listener).exitInt32Type(this);
		}
	}

	public final TypeContext type() throws RecognitionException {
		TypeContext _localctx = new TypeContext(_ctx, getState());
		enterRule(_localctx, 84, RULE_type);
		try {
			setState(496);
			_errHandler.sync(this);
			switch ( getInterpreter().adaptivePredict(_input,47,_ctx) ) {
			case 1:
				_localctx = new Int32TypeContext(_localctx);
				enterOuterAlt(_localctx, 1);
				{
				setState(481);
				match(INT32_TYPE);
				}
				break;
			case 2:
				_localctx = new Float32TypeContext(_localctx);
				enterOuterAlt(_localctx, 2);
				{
				setState(482);
				match(FLOAT32_TYPE);
				}
				break;
			case 3:
				_localctx = new BoolTypeContext(_localctx);
				enterOuterAlt(_localctx, 3);
				{
				setState(483);
				match(BOOL_TYPE);
				}
				break;
			case 4:
				_localctx = new RuneTypeContext(_localctx);
				enterOuterAlt(_localctx, 4);
				{
				setState(484);
				match(RUNE_TYPE);
				}
				break;
			case 5:
				_localctx = new StringTypeContext(_localctx);
				enterOuterAlt(_localctx, 5);
				{
				setState(485);
				match(STRING_TYPE);
				}
				break;
			case 6:
				_localctx = new ArrayTypeContext(_localctx);
				enterOuterAlt(_localctx, 6);
				{
				setState(486);
				match(T__6);
				setState(487);
				expression();
				setState(488);
				match(T__7);
				setState(489);
				type();
				}
				break;
			case 7:
				_localctx = new SliceTypeContext(_localctx);
				enterOuterAlt(_localctx, 7);
				{
				setState(491);
				match(T__6);
				setState(492);
				match(T__7);
				setState(493);
				type();
				}
				break;
			case 8:
				_localctx = new PointerTypeContext(_localctx);
				enterOuterAlt(_localctx, 8);
				{
				setState(494);
				match(T__5);
				setState(495);
				type();
				}
				break;
			}
		}
		catch (RecognitionException re) {
			_localctx.exception = re;
			_errHandler.reportError(this, re);
			_errHandler.recover(this, re);
		}
		finally {
			exitRule();
		}
		return _localctx;
	}

	public static final String _serializedATN =
		"\u0004\u0001=\u01f3\u0002\u0000\u0007\u0000\u0002\u0001\u0007\u0001\u0002"+
		"\u0002\u0007\u0002\u0002\u0003\u0007\u0003\u0002\u0004\u0007\u0004\u0002"+
		"\u0005\u0007\u0005\u0002\u0006\u0007\u0006\u0002\u0007\u0007\u0007\u0002"+
		"\b\u0007\b\u0002\t\u0007\t\u0002\n\u0007\n\u0002\u000b\u0007\u000b\u0002"+
		"\f\u0007\f\u0002\r\u0007\r\u0002\u000e\u0007\u000e\u0002\u000f\u0007\u000f"+
		"\u0002\u0010\u0007\u0010\u0002\u0011\u0007\u0011\u0002\u0012\u0007\u0012"+
		"\u0002\u0013\u0007\u0013\u0002\u0014\u0007\u0014\u0002\u0015\u0007\u0015"+
		"\u0002\u0016\u0007\u0016\u0002\u0017\u0007\u0017\u0002\u0018\u0007\u0018"+
		"\u0002\u0019\u0007\u0019\u0002\u001a\u0007\u001a\u0002\u001b\u0007\u001b"+
		"\u0002\u001c\u0007\u001c\u0002\u001d\u0007\u001d\u0002\u001e\u0007\u001e"+
		"\u0002\u001f\u0007\u001f\u0002 \u0007 \u0002!\u0007!\u0002\"\u0007\"\u0002"+
		"#\u0007#\u0002$\u0007$\u0002%\u0007%\u0002&\u0007&\u0002\'\u0007\'\u0002"+
		"(\u0007(\u0002)\u0007)\u0002*\u0007*\u0001\u0000\u0005\u0000X\b\u0000"+
		"\n\u0000\f\u0000[\t\u0000\u0001\u0000\u0001\u0000\u0001\u0001\u0001\u0001"+
		"\u0001\u0001\u0001\u0001\u0003\u0001c\b\u0001\u0001\u0002\u0001\u0002"+
		"\u0001\u0002\u0001\u0002\u0001\u0002\u0001\u0002\u0001\u0002\u0001\u0002"+
		"\u0001\u0002\u0001\u0002\u0003\u0002o\b\u0002\u0001\u0003\u0001\u0003"+
		"\u0001\u0003\u0001\u0003\u0001\u0004\u0001\u0004\u0001\u0004\u0001\u0004"+
		"\u0001\u0004\u0001\u0004\u0001\u0005\u0001\u0005\u0001\u0005\u0001\u0005"+
		"\u0003\u0005\u007f\b\u0005\u0001\u0005\u0001\u0005\u0003\u0005\u0083\b"+
		"\u0005\u0001\u0005\u0001\u0005\u0001\u0005\u0001\u0005\u0001\u0005\u0003"+
		"\u0005\u008a\b\u0005\u0001\u0005\u0001\u0005\u0001\u0005\u0001\u0005\u0001"+
		"\u0005\u0001\u0005\u0003\u0005\u0092\b\u0005\u0001\u0006\u0001\u0006\u0001"+
		"\u0006\u0005\u0006\u0097\b\u0006\n\u0006\f\u0006\u009a\t\u0006\u0001\u0007"+
		"\u0001\u0007\u0001\u0007\u0001\u0007\u0001\u0007\u0003\u0007\u00a1\b\u0007"+
		"\u0001\b\u0001\b\u0001\b\u0005\b\u00a6\b\b\n\b\f\b\u00a9\t\b\u0001\t\u0001"+
		"\t\u0001\t\u0005\t\u00ae\b\t\n\t\f\t\u00b1\t\t\u0001\n\u0001\n\u0001\n"+
		"\u0005\n\u00b6\b\n\n\n\f\n\u00b9\t\n\u0001\u000b\u0001\u000b\u0001\u000b"+
		"\u0001\u000b\u0001\u000b\u0001\u000b\u0001\u000b\u0001\u000b\u0001\u000b"+
		"\u0001\u000b\u0001\u000b\u0003\u000b\u00c6\b\u000b\u0001\f\u0001\f\u0001"+
		"\f\u0001\f\u0001\f\u0001\f\u0001\f\u0001\f\u0001\f\u0004\f\u00d1\b\f\u000b"+
		"\f\f\f\u00d2\u0001\f\u0001\f\u0001\f\u0001\f\u0001\f\u0001\f\u0001\f\u0001"+
		"\f\u0003\f\u00dd\b\f\u0001\r\u0001\r\u0001\u000e\u0001\u000e\u0001\u000e"+
		"\u0001\u000e\u0003\u000e\u00e5\b\u000e\u0001\u000f\u0001\u000f\u0001\u000f"+
		"\u0001\u000f\u0001\u000f\u0001\u000f\u0001\u000f\u0001\u000f\u0005\u000f"+
		"\u00ef\b\u000f\n\u000f\f\u000f\u00f2\t\u000f\u0001\u000f\u0001\u000f\u0003"+
		"\u000f\u00f6\b\u000f\u0001\u0010\u0001\u0010\u0001\u0010\u0001\u0010\u0005"+
		"\u0010\u00fc\b\u0010\n\u0010\f\u0010\u00ff\t\u0010\u0001\u0010\u0003\u0010"+
		"\u0102\b\u0010\u0001\u0010\u0001\u0010\u0001\u0011\u0001\u0011\u0001\u0011"+
		"\u0001\u0011\u0005\u0011\u010a\b\u0011\n\u0011\f\u0011\u010d\t\u0011\u0001"+
		"\u0012\u0001\u0012\u0001\u0012\u0005\u0012\u0112\b\u0012\n\u0012\f\u0012"+
		"\u0115\t\u0012\u0001\u0013\u0001\u0013\u0001\u0013\u0001\u0013\u0001\u0013"+
		"\u0001\u0013\u0001\u0013\u0001\u0013\u0001\u0013\u0001\u0013\u0003\u0013"+
		"\u0121\b\u0013\u0001\u0014\u0001\u0014\u0001\u0014\u0003\u0014\u0126\b"+
		"\u0014\u0001\u0014\u0001\u0014\u0001\u0014\u0001\u0015\u0001\u0015\u0001"+
		"\u0015\u0001\u0015\u0001\u0015\u0003\u0015\u0130\b\u0015\u0001\u0016\u0001"+
		"\u0016\u0001\u0016\u0003\u0016\u0135\b\u0016\u0001\u0017\u0001\u0017\u0001"+
		"\u0018\u0001\u0018\u0001\u0019\u0001\u0019\u0003\u0019\u013d\b\u0019\u0001"+
		"\u001a\u0001\u001a\u0005\u001a\u0141\b\u001a\n\u001a\f\u001a\u0144\t\u001a"+
		"\u0001\u001a\u0001\u001a\u0001\u001b\u0001\u001b\u0001\u001c\u0001\u001c"+
		"\u0001\u001d\u0001\u001d\u0001\u001d\u0005\u001d\u014f\b\u001d\n\u001d"+
		"\f\u001d\u0152\t\u001d\u0001\u001e\u0001\u001e\u0001\u001e\u0005\u001e"+
		"\u0157\b\u001e\n\u001e\f\u001e\u015a\t\u001e\u0001\u001f\u0001\u001f\u0001"+
		"\u001f\u0005\u001f\u015f\b\u001f\n\u001f\f\u001f\u0162\t\u001f\u0001 "+
		"\u0001 \u0001 \u0005 \u0167\b \n \f \u016a\t \u0001!\u0001!\u0001!\u0005"+
		"!\u016f\b!\n!\f!\u0172\t!\u0001\"\u0001\"\u0001\"\u0005\"\u0177\b\"\n"+
		"\"\f\"\u017a\t\"\u0001#\u0001#\u0001#\u0001#\u0001#\u0001#\u0001#\u0001"+
		"#\u0001#\u0003#\u0185\b#\u0001$\u0001$\u0001$\u0001$\u0001$\u0001$\u0001"+
		"$\u0001$\u0001$\u0001$\u0003$\u0191\b$\u0001$\u0001$\u0003$\u0195\b$\u0001"+
		"$\u0001$\u0001$\u0001$\u0001$\u0001$\u0004$\u019d\b$\u000b$\f$\u019e\u0001"+
		"$\u0001$\u0001$\u0001$\u0001$\u0001$\u0001$\u0001$\u0001$\u0001$\u0003"+
		"$\u01ab\b$\u0001%\u0001%\u0001%\u0001%\u0001%\u0001%\u0001%\u0003%\u01b4"+
		"\b%\u0001%\u0001%\u0001%\u0001%\u0001%\u0001%\u0001%\u0003%\u01bd\b%\u0001"+
		"%\u0001%\u0003%\u01c1\b%\u0001&\u0001&\u0001&\u0005&\u01c6\b&\n&\f&\u01c9"+
		"\t&\u0001&\u0003&\u01cc\b&\u0001\'\u0001\'\u0001\'\u0003\'\u01d1\b\'\u0001"+
		"\'\u0001\'\u0001(\u0001(\u0001(\u0005(\u01d8\b(\n(\f(\u01db\t(\u0001)"+
		"\u0001)\u0001)\u0003)\u01e0\b)\u0001*\u0001*\u0001*\u0001*\u0001*\u0001"+
		"*\u0001*\u0001*\u0001*\u0001*\u0001*\u0001*\u0001*\u0001*\u0001*\u0003"+
		"*\u01f1\b*\u0001*\u0000\u0000+\u0000\u0002\u0004\u0006\b\n\f\u000e\u0010"+
		"\u0012\u0014\u0016\u0018\u001a\u001c\u001e \"$&(*,.02468:<>@BDFHJLNPR"+
		"T\u0000\u0005\u0002\u0000\u0001\u0001\t\f\u0001\u0000\u0013\u0014\u0001"+
		"\u0000\u0015\u0018\u0001\u0000\u0019\u001a\u0002\u0000\u0006\u0006\u001b"+
		"\u001c\u021d\u0000Y\u0001\u0000\u0000\u0000\u0002b\u0001\u0000\u0000\u0000"+
		"\u0004n\u0001\u0000\u0000\u0000\u0006p\u0001\u0000\u0000\u0000\bt\u0001"+
		"\u0000\u0000\u0000\n\u0091\u0001\u0000\u0000\u0000\f\u0093\u0001\u0000"+
		"\u0000\u0000\u000e\u00a0\u0001\u0000\u0000\u0000\u0010\u00a2\u0001\u0000"+
		"\u0000\u0000\u0012\u00aa\u0001\u0000\u0000\u0000\u0014\u00b2\u0001\u0000"+
		"\u0000\u0000\u0016\u00c5\u0001\u0000\u0000\u0000\u0018\u00dc\u0001\u0000"+
		"\u0000\u0000\u001a\u00de\u0001\u0000\u0000\u0000\u001c\u00e4\u0001\u0000"+
		"\u0000\u0000\u001e\u00e6\u0001\u0000\u0000\u0000 \u00f7\u0001\u0000\u0000"+
		"\u0000\"\u0105\u0001\u0000\u0000\u0000$\u010e\u0001\u0000\u0000\u0000"+
		"&\u0120\u0001\u0000\u0000\u0000(\u0122\u0001\u0000\u0000\u0000*\u012f"+
		"\u0001\u0000\u0000\u0000,\u0134\u0001\u0000\u0000\u0000.\u0136\u0001\u0000"+
		"\u0000\u00000\u0138\u0001\u0000\u0000\u00002\u013a\u0001\u0000\u0000\u0000"+
		"4\u013e\u0001\u0000\u0000\u00006\u0147\u0001\u0000\u0000\u00008\u0149"+
		"\u0001\u0000\u0000\u0000:\u014b\u0001\u0000\u0000\u0000<\u0153\u0001\u0000"+
		"\u0000\u0000>\u015b\u0001\u0000\u0000\u0000@\u0163\u0001\u0000\u0000\u0000"+
		"B\u016b\u0001\u0000\u0000\u0000D\u0173\u0001\u0000\u0000\u0000F\u0184"+
		"\u0001\u0000\u0000\u0000H\u01aa\u0001\u0000\u0000\u0000J\u01c0\u0001\u0000"+
		"\u0000\u0000L\u01c2\u0001\u0000\u0000\u0000N\u01cd\u0001\u0000\u0000\u0000"+
		"P\u01d4\u0001\u0000\u0000\u0000R\u01df\u0001\u0000\u0000\u0000T\u01f0"+
		"\u0001\u0000\u0000\u0000VX\u0003\u0002\u0001\u0000WV\u0001\u0000\u0000"+
		"\u0000X[\u0001\u0000\u0000\u0000YW\u0001\u0000\u0000\u0000YZ\u0001\u0000"+
		"\u0000\u0000Z\\\u0001\u0000\u0000\u0000[Y\u0001\u0000\u0000\u0000\\]\u0005"+
		"\u0000\u0000\u0001]\u0001\u0001\u0000\u0000\u0000^c\u0003\u0004\u0002"+
		"\u0000_c\u0003\b\u0004\u0000`c\u0003\n\u0005\u0000ac\u0003\u0016\u000b"+
		"\u0000b^\u0001\u0000\u0000\u0000b_\u0001\u0000\u0000\u0000b`\u0001\u0000"+
		"\u0000\u0000ba\u0001\u0000\u0000\u0000c\u0003\u0001\u0000\u0000\u0000"+
		"de\u0005 \u0000\u0000ef\u0003\u0012\t\u0000fg\u0003T*\u0000go\u0001\u0000"+
		"\u0000\u0000hi\u0005 \u0000\u0000ij\u0003\u0012\t\u0000jk\u0003T*\u0000"+
		"kl\u0005\u0001\u0000\u0000lm\u0003\u0014\n\u0000mo\u0001\u0000\u0000\u0000"+
		"nd\u0001\u0000\u0000\u0000nh\u0001\u0000\u0000\u0000o\u0005\u0001\u0000"+
		"\u0000\u0000pq\u0003\u0012\t\u0000qr\u0005\u0002\u0000\u0000rs\u0003\u0014"+
		"\n\u0000s\u0007\u0001\u0000\u0000\u0000tu\u0005!\u0000\u0000uv\u0005:"+
		"\u0000\u0000vw\u0003T*\u0000wx\u0005\u0001\u0000\u0000xy\u00038\u001c"+
		"\u0000y\t\u0001\u0000\u0000\u0000z{\u0005\"\u0000\u0000{|\u0005:\u0000"+
		"\u0000|~\u0005\u0003\u0000\u0000}\u007f\u0003\f\u0006\u0000~}\u0001\u0000"+
		"\u0000\u0000~\u007f\u0001\u0000\u0000\u0000\u007f\u0080\u0001\u0000\u0000"+
		"\u0000\u0080\u0082\u0005\u0004\u0000\u0000\u0081\u0083\u0003T*\u0000\u0082"+
		"\u0081\u0001\u0000\u0000\u0000\u0082\u0083\u0001\u0000\u0000\u0000\u0083"+
		"\u0084\u0001\u0000\u0000\u0000\u0084\u0092\u00034\u001a\u0000\u0085\u0086"+
		"\u0005\"\u0000\u0000\u0086\u0087\u0005:\u0000\u0000\u0087\u0089\u0005"+
		"\u0003\u0000\u0000\u0088\u008a\u0003\f\u0006\u0000\u0089\u0088\u0001\u0000"+
		"\u0000\u0000\u0089\u008a\u0001\u0000\u0000\u0000\u008a\u008b\u0001\u0000"+
		"\u0000\u0000\u008b\u008c\u0005\u0004\u0000\u0000\u008c\u008d\u0005\u0003"+
		"\u0000\u0000\u008d\u008e\u0003\u0010\b\u0000\u008e\u008f\u0005\u0004\u0000"+
		"\u0000\u008f\u0090\u00034\u001a\u0000\u0090\u0092\u0001\u0000\u0000\u0000"+
		"\u0091z\u0001\u0000\u0000\u0000\u0091\u0085\u0001\u0000\u0000\u0000\u0092"+
		"\u000b\u0001\u0000\u0000\u0000\u0093\u0098\u0003\u000e\u0007\u0000\u0094"+
		"\u0095\u0005\u0005\u0000\u0000\u0095\u0097\u0003\u000e\u0007\u0000\u0096"+
		"\u0094\u0001\u0000\u0000\u0000\u0097\u009a\u0001\u0000\u0000\u0000\u0098"+
		"\u0096\u0001\u0000\u0000\u0000\u0098\u0099\u0001\u0000\u0000\u0000\u0099"+
		"\r\u0001\u0000\u0000\u0000\u009a\u0098\u0001\u0000\u0000\u0000\u009b\u009c"+
		"\u0005:\u0000\u0000\u009c\u00a1\u0003T*\u0000\u009d\u009e\u0005\u0006"+
		"\u0000\u0000\u009e\u009f\u0005:\u0000\u0000\u009f\u00a1\u0003T*\u0000"+
		"\u00a0\u009b\u0001\u0000\u0000\u0000\u00a0\u009d\u0001\u0000\u0000\u0000"+
		"\u00a1\u000f\u0001\u0000\u0000\u0000\u00a2\u00a7\u0003T*\u0000\u00a3\u00a4"+
		"\u0005\u0005\u0000\u0000\u00a4\u00a6\u0003T*\u0000\u00a5\u00a3\u0001\u0000"+
		"\u0000\u0000\u00a6\u00a9\u0001\u0000\u0000\u0000\u00a7\u00a5\u0001\u0000"+
		"\u0000\u0000\u00a7\u00a8\u0001\u0000\u0000\u0000\u00a8\u0011\u0001\u0000"+
		"\u0000\u0000\u00a9\u00a7\u0001\u0000\u0000\u0000\u00aa\u00af\u0005:\u0000"+
		"\u0000\u00ab\u00ac\u0005\u0005\u0000\u0000\u00ac\u00ae\u0005:\u0000\u0000"+
		"\u00ad\u00ab\u0001\u0000\u0000\u0000\u00ae\u00b1\u0001\u0000\u0000\u0000"+
		"\u00af\u00ad\u0001\u0000\u0000\u0000\u00af\u00b0\u0001\u0000\u0000\u0000"+
		"\u00b0\u0013\u0001\u0000\u0000\u0000\u00b1\u00af\u0001\u0000\u0000\u0000"+
		"\u00b2\u00b7\u00038\u001c\u0000\u00b3\u00b4\u0005\u0005\u0000\u0000\u00b4"+
		"\u00b6\u00038\u001c\u0000\u00b5\u00b3\u0001\u0000\u0000\u0000\u00b6\u00b9"+
		"\u0001\u0000\u0000\u0000\u00b7\u00b5\u0001\u0000\u0000\u0000\u00b7\u00b8"+
		"\u0001\u0000\u0000\u0000\u00b8\u0015\u0001\u0000\u0000\u0000\u00b9\u00b7"+
		"\u0001\u0000\u0000\u0000\u00ba\u00c6\u0003\u0006\u0003\u0000\u00bb\u00c6"+
		"\u0003\u0018\f\u0000\u00bc\u00c6\u0003\u001e\u000f\u0000\u00bd\u00c6\u0003"+
		" \u0010\u0000\u00be\u00c6\u0003&\u0013\u0000\u00bf\u00c6\u0003.\u0017"+
		"\u0000\u00c0\u00c6\u00030\u0018\u0000\u00c1\u00c6\u00032\u0019\u0000\u00c2"+
		"\u00c6\u0003\u001c\u000e\u0000\u00c3\u00c6\u00034\u001a\u0000\u00c4\u00c6"+
		"\u00036\u001b\u0000\u00c5\u00ba\u0001\u0000\u0000\u0000\u00c5\u00bb\u0001"+
		"\u0000\u0000\u0000\u00c5\u00bc\u0001\u0000\u0000\u0000\u00c5\u00bd\u0001"+
		"\u0000\u0000\u0000\u00c5\u00be\u0001\u0000\u0000\u0000\u00c5\u00bf\u0001"+
		"\u0000\u0000\u0000\u00c5\u00c0\u0001\u0000\u0000\u0000\u00c5\u00c1\u0001"+
		"\u0000\u0000\u0000\u00c5\u00c2\u0001\u0000\u0000\u0000\u00c5\u00c3\u0001"+
		"\u0000\u0000\u0000\u00c5\u00c4\u0001\u0000\u0000\u0000\u00c6\u0017\u0001"+
		"\u0000\u0000\u0000\u00c7\u00c8\u0005:\u0000\u0000\u00c8\u00c9\u0003\u001a"+
		"\r\u0000\u00c9\u00ca\u00038\u001c\u0000\u00ca\u00dd\u0001\u0000\u0000"+
		"\u0000\u00cb\u00d0\u0005:\u0000\u0000\u00cc\u00cd\u0005\u0007\u0000\u0000"+
		"\u00cd\u00ce\u00038\u001c\u0000\u00ce\u00cf\u0005\b\u0000\u0000\u00cf"+
		"\u00d1\u0001\u0000\u0000\u0000\u00d0\u00cc\u0001\u0000\u0000\u0000\u00d1"+
		"\u00d2\u0001\u0000\u0000\u0000\u00d2\u00d0\u0001\u0000\u0000\u0000\u00d2"+
		"\u00d3\u0001\u0000\u0000\u0000\u00d3\u00d4\u0001\u0000\u0000\u0000\u00d4"+
		"\u00d5\u0003\u001a\r\u0000\u00d5\u00d6\u00038\u001c\u0000\u00d6\u00dd"+
		"\u0001\u0000\u0000\u0000\u00d7\u00d8\u0005\u0006\u0000\u0000\u00d8\u00d9"+
		"\u0005:\u0000\u0000\u00d9\u00da\u0003\u001a\r\u0000\u00da\u00db\u0003"+
		"8\u001c\u0000\u00db\u00dd\u0001\u0000\u0000\u0000\u00dc\u00c7\u0001\u0000"+
		"\u0000\u0000\u00dc\u00cb\u0001\u0000\u0000\u0000\u00dc\u00d7\u0001\u0000"+
		"\u0000\u0000\u00dd\u0019\u0001\u0000\u0000\u0000\u00de\u00df\u0007\u0000"+
		"\u0000\u0000\u00df\u001b\u0001\u0000\u0000\u0000\u00e0\u00e1\u0005:\u0000"+
		"\u0000\u00e1\u00e5\u0005\r\u0000\u0000\u00e2\u00e3\u0005:\u0000\u0000"+
		"\u00e3\u00e5\u0005\u000e\u0000\u0000\u00e4\u00e0\u0001\u0000\u0000\u0000"+
		"\u00e4\u00e2\u0001\u0000\u0000\u0000\u00e5\u001d\u0001\u0000\u0000\u0000"+
		"\u00e6\u00e7\u0005#\u0000\u0000\u00e7\u00e8\u00038\u001c\u0000\u00e8\u00f0"+
		"\u00034\u001a\u0000\u00e9\u00ea\u0005$\u0000\u0000\u00ea\u00eb\u0005#"+
		"\u0000\u0000\u00eb\u00ec\u00038\u001c\u0000\u00ec\u00ed\u00034\u001a\u0000"+
		"\u00ed\u00ef\u0001\u0000\u0000\u0000\u00ee\u00e9\u0001\u0000\u0000\u0000"+
		"\u00ef\u00f2\u0001\u0000\u0000\u0000\u00f0\u00ee\u0001\u0000\u0000\u0000"+
		"\u00f0\u00f1\u0001\u0000\u0000\u0000\u00f1\u00f5\u0001\u0000\u0000\u0000"+
		"\u00f2\u00f0\u0001\u0000\u0000\u0000\u00f3\u00f4\u0005$\u0000\u0000\u00f4"+
		"\u00f6\u00034\u001a\u0000\u00f5\u00f3\u0001\u0000\u0000\u0000\u00f5\u00f6"+
		"\u0001\u0000\u0000\u0000\u00f6\u001f\u0001\u0000\u0000\u0000\u00f7\u00f8"+
		"\u0005%\u0000\u0000\u00f8\u00f9\u00038\u001c\u0000\u00f9\u00fd\u0005\u000f"+
		"\u0000\u0000\u00fa\u00fc\u0003\"\u0011\u0000\u00fb\u00fa\u0001\u0000\u0000"+
		"\u0000\u00fc\u00ff\u0001\u0000\u0000\u0000\u00fd\u00fb\u0001\u0000\u0000"+
		"\u0000\u00fd\u00fe\u0001\u0000\u0000\u0000\u00fe\u0101\u0001\u0000\u0000"+
		"\u0000\u00ff\u00fd\u0001\u0000\u0000\u0000\u0100\u0102\u0003$\u0012\u0000"+
		"\u0101\u0100\u0001\u0000\u0000\u0000\u0101\u0102\u0001\u0000\u0000\u0000"+
		"\u0102\u0103\u0001\u0000\u0000\u0000\u0103\u0104\u0005\u0010\u0000\u0000"+
		"\u0104!\u0001\u0000\u0000\u0000\u0105\u0106\u0005&\u0000\u0000\u0106\u0107"+
		"\u0003\u0014\n\u0000\u0107\u010b\u0005\u0011\u0000\u0000\u0108\u010a\u0003"+
		"\u0016\u000b\u0000\u0109\u0108\u0001\u0000\u0000\u0000\u010a\u010d\u0001"+
		"\u0000\u0000\u0000\u010b\u0109\u0001\u0000\u0000\u0000\u010b\u010c\u0001"+
		"\u0000\u0000\u0000\u010c#\u0001\u0000\u0000\u0000\u010d\u010b\u0001\u0000"+
		"\u0000\u0000\u010e\u010f\u0005\'\u0000\u0000\u010f\u0113\u0005\u0011\u0000"+
		"\u0000\u0110\u0112\u0003\u0016\u000b\u0000\u0111\u0110\u0001\u0000\u0000"+
		"\u0000\u0112\u0115\u0001\u0000\u0000\u0000\u0113\u0111\u0001\u0000\u0000"+
		"\u0000\u0113\u0114\u0001\u0000\u0000\u0000\u0114%\u0001\u0000\u0000\u0000"+
		"\u0115\u0113\u0001\u0000\u0000\u0000\u0116\u0117\u0005(\u0000\u0000\u0117"+
		"\u0118\u0003(\u0014\u0000\u0118\u0119\u00034\u001a\u0000\u0119\u0121\u0001"+
		"\u0000\u0000\u0000\u011a\u011b\u0005(\u0000\u0000\u011b\u011c\u00038\u001c"+
		"\u0000\u011c\u011d\u00034\u001a\u0000\u011d\u0121\u0001\u0000\u0000\u0000"+
		"\u011e\u011f\u0005(\u0000\u0000\u011f\u0121\u00034\u001a\u0000\u0120\u0116"+
		"\u0001\u0000\u0000\u0000\u0120\u011a\u0001\u0000\u0000\u0000\u0120\u011e"+
		"\u0001\u0000\u0000\u0000\u0121\'\u0001\u0000\u0000\u0000\u0122\u0123\u0003"+
		"*\u0015\u0000\u0123\u0125\u0005\u0012\u0000\u0000\u0124\u0126\u00038\u001c"+
		"\u0000\u0125\u0124\u0001\u0000\u0000\u0000\u0125\u0126\u0001\u0000\u0000"+
		"\u0000\u0126\u0127\u0001\u0000\u0000\u0000\u0127\u0128\u0005\u0012\u0000"+
		"\u0000\u0128\u0129\u0003,\u0016\u0000\u0129)\u0001\u0000\u0000\u0000\u012a"+
		"\u0130\u0003\u0004\u0002\u0000\u012b\u0130\u0003\u0006\u0003\u0000\u012c"+
		"\u0130\u0003\u0018\f\u0000\u012d\u0130\u0003\u001c\u000e\u0000\u012e\u0130"+
		"\u0001\u0000\u0000\u0000\u012f\u012a\u0001\u0000\u0000\u0000\u012f\u012b"+
		"\u0001\u0000\u0000\u0000\u012f\u012c\u0001\u0000\u0000\u0000\u012f\u012d"+
		"\u0001\u0000\u0000\u0000\u012f\u012e\u0001\u0000\u0000\u0000\u0130+\u0001"+
		"\u0000\u0000\u0000\u0131\u0135\u0003\u0018\f\u0000\u0132\u0135\u0003\u001c"+
		"\u000e\u0000\u0133\u0135\u0001\u0000\u0000\u0000\u0134\u0131\u0001\u0000"+
		"\u0000\u0000\u0134\u0132\u0001\u0000\u0000\u0000\u0134\u0133\u0001\u0000"+
		"\u0000\u0000\u0135-\u0001\u0000\u0000\u0000\u0136\u0137\u0005)\u0000\u0000"+
		"\u0137/\u0001\u0000\u0000\u0000\u0138\u0139\u0005*\u0000\u0000\u01391"+
		"\u0001\u0000\u0000\u0000\u013a\u013c\u0005+\u0000\u0000\u013b\u013d\u0003"+
		"\u0014\n\u0000\u013c\u013b\u0001\u0000\u0000\u0000\u013c\u013d\u0001\u0000"+
		"\u0000\u0000\u013d3\u0001\u0000\u0000\u0000\u013e\u0142\u0005\u000f\u0000"+
		"\u0000\u013f\u0141\u0003\u0002\u0001\u0000\u0140\u013f\u0001\u0000\u0000"+
		"\u0000\u0141\u0144\u0001\u0000\u0000\u0000\u0142\u0140\u0001\u0000\u0000"+
		"\u0000\u0142\u0143\u0001\u0000\u0000\u0000\u0143\u0145\u0001\u0000\u0000"+
		"\u0000\u0144\u0142\u0001\u0000\u0000\u0000\u0145\u0146\u0005\u0010\u0000"+
		"\u0000\u01465\u0001\u0000\u0000\u0000\u0147\u0148\u00038\u001c\u0000\u0148"+
		"7\u0001\u0000\u0000\u0000\u0149\u014a\u0003:\u001d\u0000\u014a9\u0001"+
		"\u0000\u0000\u0000\u014b\u0150\u0003<\u001e\u0000\u014c\u014d\u00055\u0000"+
		"\u0000\u014d\u014f\u0003<\u001e\u0000\u014e\u014c\u0001\u0000\u0000\u0000"+
		"\u014f\u0152\u0001\u0000\u0000\u0000\u0150\u014e\u0001\u0000\u0000\u0000"+
		"\u0150\u0151\u0001\u0000\u0000\u0000\u0151;\u0001\u0000\u0000\u0000\u0152"+
		"\u0150\u0001\u0000\u0000\u0000\u0153\u0158\u0003>\u001f\u0000\u0154\u0155"+
		"\u00054\u0000\u0000\u0155\u0157\u0003>\u001f\u0000\u0156\u0154\u0001\u0000"+
		"\u0000\u0000\u0157\u015a\u0001\u0000\u0000\u0000\u0158\u0156\u0001\u0000"+
		"\u0000\u0000\u0158\u0159\u0001\u0000\u0000\u0000\u0159=\u0001\u0000\u0000"+
		"\u0000\u015a\u0158\u0001\u0000\u0000\u0000\u015b\u0160\u0003@ \u0000\u015c"+
		"\u015d\u0007\u0001\u0000\u0000\u015d\u015f\u0003@ \u0000\u015e\u015c\u0001"+
		"\u0000\u0000\u0000\u015f\u0162\u0001\u0000\u0000\u0000\u0160\u015e\u0001"+
		"\u0000\u0000\u0000\u0160\u0161\u0001\u0000\u0000\u0000\u0161?\u0001\u0000"+
		"\u0000\u0000\u0162\u0160\u0001\u0000\u0000\u0000\u0163\u0168\u0003B!\u0000"+
		"\u0164\u0165\u0007\u0002\u0000\u0000\u0165\u0167\u0003B!\u0000\u0166\u0164"+
		"\u0001\u0000\u0000\u0000\u0167\u016a\u0001\u0000\u0000\u0000\u0168\u0166"+
		"\u0001\u0000\u0000\u0000\u0168\u0169\u0001\u0000\u0000\u0000\u0169A\u0001"+
		"\u0000\u0000\u0000\u016a\u0168\u0001\u0000\u0000\u0000\u016b\u0170\u0003"+
		"D\"\u0000\u016c\u016d\u0007\u0003\u0000\u0000\u016d\u016f\u0003D\"\u0000"+
		"\u016e\u016c\u0001\u0000\u0000\u0000\u016f\u0172\u0001\u0000\u0000\u0000"+
		"\u0170\u016e\u0001\u0000\u0000\u0000\u0170\u0171\u0001\u0000\u0000\u0000"+
		"\u0171C\u0001\u0000\u0000\u0000\u0172\u0170\u0001\u0000\u0000\u0000\u0173"+
		"\u0178\u0003F#\u0000\u0174\u0175\u0007\u0004\u0000\u0000\u0175\u0177\u0003"+
		"F#\u0000\u0176\u0174\u0001\u0000\u0000\u0000\u0177\u017a\u0001\u0000\u0000"+
		"\u0000\u0178\u0176\u0001\u0000\u0000\u0000\u0178\u0179\u0001\u0000\u0000"+
		"\u0000\u0179E\u0001\u0000\u0000\u0000\u017a\u0178\u0001\u0000\u0000\u0000"+
		"\u017b\u0185\u0003H$\u0000\u017c\u017d\u0005\u001a\u0000\u0000\u017d\u0185"+
		"\u0003F#\u0000\u017e\u017f\u0005\u001d\u0000\u0000\u017f\u0185\u0003F"+
		"#\u0000\u0180\u0181\u0005\u001e\u0000\u0000\u0181\u0185\u0005:\u0000\u0000"+
		"\u0182\u0183\u0005\u0006\u0000\u0000\u0183\u0185\u0003F#\u0000\u0184\u017b"+
		"\u0001\u0000\u0000\u0000\u0184\u017c\u0001\u0000\u0000\u0000\u0184\u017e"+
		"\u0001\u0000\u0000\u0000\u0184\u0180\u0001\u0000\u0000\u0000\u0184\u0182"+
		"\u0001\u0000\u0000\u0000\u0185G\u0001\u0000\u0000\u0000\u0186\u01ab\u0005"+
		"6\u0000\u0000\u0187\u01ab\u00057\u0000\u0000\u0188\u01ab\u00058\u0000"+
		"\u0000\u0189\u01ab\u00059\u0000\u0000\u018a\u01ab\u0005,\u0000\u0000\u018b"+
		"\u01ab\u0005-\u0000\u0000\u018c\u01ab\u0005.\u0000\u0000\u018d\u0190\u0005"+
		":\u0000\u0000\u018e\u018f\u0005\u001f\u0000\u0000\u018f\u0191\u0005:\u0000"+
		"\u0000\u0190\u018e\u0001\u0000\u0000\u0000\u0190\u0191\u0001\u0000\u0000"+
		"\u0000\u0191\u0192\u0001\u0000\u0000\u0000\u0192\u0194\u0005\u0003\u0000"+
		"\u0000\u0193\u0195\u0003P(\u0000\u0194\u0193\u0001\u0000\u0000\u0000\u0194"+
		"\u0195\u0001\u0000\u0000\u0000\u0195\u0196\u0001\u0000\u0000\u0000\u0196"+
		"\u01ab\u0005\u0004\u0000\u0000\u0197\u019c\u0005:\u0000\u0000\u0198\u0199"+
		"\u0005\u0007\u0000\u0000\u0199\u019a\u00038\u001c\u0000\u019a\u019b\u0005"+
		"\b\u0000\u0000\u019b\u019d\u0001\u0000\u0000\u0000\u019c\u0198\u0001\u0000"+
		"\u0000\u0000\u019d\u019e\u0001\u0000\u0000\u0000\u019e\u019c\u0001\u0000"+
		"\u0000\u0000\u019e\u019f\u0001\u0000\u0000\u0000\u019f\u01ab\u0001\u0000"+
		"\u0000\u0000\u01a0\u01ab\u0005:\u0000\u0000\u01a1\u01a2\u0005\u0003\u0000"+
		"\u0000\u01a2\u01a3\u00038\u001c\u0000\u01a3\u01a4\u0005\u0004\u0000\u0000"+
		"\u01a4\u01ab\u0001\u0000\u0000\u0000\u01a5\u01ab\u0003J%\u0000\u01a6\u01a7"+
		"\u0005\u000f\u0000\u0000\u01a7\u01a8\u0003\u0014\n\u0000\u01a8\u01a9\u0005"+
		"\u0010\u0000\u0000\u01a9\u01ab\u0001\u0000\u0000\u0000\u01aa\u0186\u0001"+
		"\u0000\u0000\u0000\u01aa\u0187\u0001\u0000\u0000\u0000\u01aa\u0188\u0001"+
		"\u0000\u0000\u0000\u01aa\u0189\u0001\u0000\u0000\u0000\u01aa\u018a\u0001"+
		"\u0000\u0000\u0000\u01aa\u018b\u0001\u0000\u0000\u0000\u01aa\u018c\u0001"+
		"\u0000\u0000\u0000\u01aa\u018d\u0001\u0000\u0000\u0000\u01aa\u0197\u0001"+
		"\u0000\u0000\u0000\u01aa\u01a0\u0001\u0000\u0000\u0000\u01aa\u01a1\u0001"+
		"\u0000\u0000\u0000\u01aa\u01a5\u0001\u0000\u0000\u0000\u01aa\u01a6\u0001"+
		"\u0000\u0000\u0000\u01abI\u0001\u0000\u0000\u0000\u01ac\u01ad\u0005\u0007"+
		"\u0000\u0000\u01ad\u01ae\u00038\u001c\u0000\u01ae\u01af\u0005\b\u0000"+
		"\u0000\u01af\u01b0\u0003T*\u0000\u01b0\u01b3\u0005\u000f\u0000\u0000\u01b1"+
		"\u01b4\u0003\u0014\n\u0000\u01b2\u01b4\u0003L&\u0000\u01b3\u01b1\u0001"+
		"\u0000\u0000\u0000\u01b3\u01b2\u0001\u0000\u0000\u0000\u01b3\u01b4\u0001"+
		"\u0000\u0000\u0000\u01b4\u01b5\u0001\u0000\u0000\u0000\u01b5\u01b6\u0005"+
		"\u0010\u0000\u0000\u01b6\u01c1\u0001\u0000\u0000\u0000\u01b7\u01b8\u0005"+
		"\u0007\u0000\u0000\u01b8\u01b9\u0005\b\u0000\u0000\u01b9\u01ba\u0003T"+
		"*\u0000\u01ba\u01bc\u0005\u000f\u0000\u0000\u01bb\u01bd\u0003\u0014\n"+
		"\u0000\u01bc\u01bb\u0001\u0000\u0000\u0000\u01bc\u01bd\u0001\u0000\u0000"+
		"\u0000\u01bd\u01be\u0001\u0000\u0000\u0000\u01be\u01bf\u0005\u0010\u0000"+
		"\u0000\u01bf\u01c1\u0001\u0000\u0000\u0000\u01c0\u01ac\u0001\u0000\u0000"+
		"\u0000\u01c0\u01b7\u0001\u0000\u0000\u0000\u01c1K\u0001\u0000\u0000\u0000"+
		"\u01c2\u01c7\u0003N\'\u0000\u01c3\u01c4\u0005\u0005\u0000\u0000\u01c4"+
		"\u01c6\u0003N\'\u0000\u01c5\u01c3\u0001\u0000\u0000\u0000\u01c6\u01c9"+
		"\u0001\u0000\u0000\u0000\u01c7\u01c5\u0001\u0000\u0000\u0000\u01c7\u01c8"+
		"\u0001\u0000\u0000\u0000\u01c8\u01cb\u0001\u0000\u0000\u0000\u01c9\u01c7"+
		"\u0001\u0000\u0000\u0000\u01ca\u01cc\u0005\u0005\u0000\u0000\u01cb\u01ca"+
		"\u0001\u0000\u0000\u0000\u01cb\u01cc\u0001\u0000\u0000\u0000\u01ccM\u0001"+
		"\u0000\u0000\u0000\u01cd\u01ce\u0005\u000f\u0000\u0000\u01ce\u01d0\u0003"+
		"\u0014\n\u0000\u01cf\u01d1\u0005\u0005\u0000\u0000\u01d0\u01cf\u0001\u0000"+
		"\u0000\u0000\u01d0\u01d1\u0001\u0000\u0000\u0000\u01d1\u01d2\u0001\u0000"+
		"\u0000\u0000\u01d2\u01d3\u0005\u0010\u0000\u0000\u01d3O\u0001\u0000\u0000"+
		"\u0000\u01d4\u01d9\u0003R)\u0000\u01d5\u01d6\u0005\u0005\u0000\u0000\u01d6"+
		"\u01d8\u0003R)\u0000\u01d7\u01d5\u0001\u0000\u0000\u0000\u01d8\u01db\u0001"+
		"\u0000\u0000\u0000\u01d9\u01d7\u0001\u0000\u0000\u0000\u01d9\u01da\u0001"+
		"\u0000\u0000\u0000\u01daQ\u0001\u0000\u0000\u0000\u01db\u01d9\u0001\u0000"+
		"\u0000\u0000\u01dc\u01e0\u00038\u001c\u0000\u01dd\u01de\u0005\u001e\u0000"+
		"\u0000\u01de\u01e0\u0005:\u0000\u0000\u01df\u01dc\u0001\u0000\u0000\u0000"+
		"\u01df\u01dd\u0001\u0000\u0000\u0000\u01e0S\u0001\u0000\u0000\u0000\u01e1"+
		"\u01f1\u0005/\u0000\u0000\u01e2\u01f1\u00050\u0000\u0000\u01e3\u01f1\u0005"+
		"1\u0000\u0000\u01e4\u01f1\u00052\u0000\u0000\u01e5\u01f1\u00053\u0000"+
		"\u0000\u01e6\u01e7\u0005\u0007\u0000\u0000\u01e7\u01e8\u00038\u001c\u0000"+
		"\u01e8\u01e9\u0005\b\u0000\u0000\u01e9\u01ea\u0003T*\u0000\u01ea\u01f1"+
		"\u0001\u0000\u0000\u0000\u01eb\u01ec\u0005\u0007\u0000\u0000\u01ec\u01ed"+
		"\u0005\b\u0000\u0000\u01ed\u01f1\u0003T*\u0000\u01ee\u01ef\u0005\u0006"+
		"\u0000\u0000\u01ef\u01f1\u0003T*\u0000\u01f0\u01e1\u0001\u0000\u0000\u0000"+
		"\u01f0\u01e2\u0001\u0000\u0000\u0000\u01f0\u01e3\u0001\u0000\u0000\u0000"+
		"\u01f0\u01e4\u0001\u0000\u0000\u0000\u01f0\u01e5\u0001\u0000\u0000\u0000"+
		"\u01f0\u01e6\u0001\u0000\u0000\u0000\u01f0\u01eb\u0001\u0000\u0000\u0000"+
		"\u01f0\u01ee\u0001\u0000\u0000\u0000\u01f1U\u0001\u0000\u0000\u00000Y"+
		"bn~\u0082\u0089\u0091\u0098\u00a0\u00a7\u00af\u00b7\u00c5\u00d2\u00dc"+
		"\u00e4\u00f0\u00f5\u00fd\u0101\u010b\u0113\u0120\u0125\u012f\u0134\u013c"+
		"\u0142\u0150\u0158\u0160\u0168\u0170\u0178\u0184\u0190\u0194\u019e\u01aa"+
		"\u01b3\u01bc\u01c0\u01c7\u01cb\u01d0\u01d9\u01df\u01f0";
	public static final ATN _ATN =
		new ATNDeserializer().deserialize(_serializedATN.toCharArray());
	static {
		_decisionToDFA = new DFA[_ATN.getNumberOfDecisions()];
		for (int i = 0; i < _ATN.getNumberOfDecisions(); i++) {
			_decisionToDFA[i] = new DFA(_ATN.getDecisionState(i), i);
		}
	}
}