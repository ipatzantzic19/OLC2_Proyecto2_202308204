<?php

/*
 * Generated from Golampi.g4 by ANTLR 4.13.0
 */

namespace {
	use Antlr\Antlr4\Runtime\Atn\ATN;
	use Antlr\Antlr4\Runtime\Atn\ATNDeserializer;
	use Antlr\Antlr4\Runtime\Atn\ParserATNSimulator;
	use Antlr\Antlr4\Runtime\Dfa\DFA;
	use Antlr\Antlr4\Runtime\Error\Exceptions\FailedPredicateException;
	use Antlr\Antlr4\Runtime\Error\Exceptions\NoViableAltException;
	use Antlr\Antlr4\Runtime\PredictionContexts\PredictionContextCache;
	use Antlr\Antlr4\Runtime\Error\Exceptions\RecognitionException;
	use Antlr\Antlr4\Runtime\RuleContext;
	use Antlr\Antlr4\Runtime\Token;
	use Antlr\Antlr4\Runtime\TokenStream;
	use Antlr\Antlr4\Runtime\Vocabulary;
	use Antlr\Antlr4\Runtime\VocabularyImpl;
	use Antlr\Antlr4\Runtime\RuntimeMetaData;
	use Antlr\Antlr4\Runtime\Parser;

	final class GolampiParser extends Parser
	{
		public const T__0 = 1, T__1 = 2, T__2 = 3, T__3 = 4, T__4 = 5, T__5 = 6, 
               T__6 = 7, T__7 = 8, T__8 = 9, T__9 = 10, T__10 = 11, T__11 = 12, 
               T__12 = 13, T__13 = 14, T__14 = 15, T__15 = 16, T__16 = 17, 
               T__17 = 18, T__18 = 19, T__19 = 20, T__20 = 21, T__21 = 22, 
               T__22 = 23, T__23 = 24, T__24 = 25, T__25 = 26, T__26 = 27, 
               T__27 = 28, T__28 = 29, T__29 = 30, T__30 = 31, T__31 = 32, 
               VAR = 33, CONST = 34, FUNC = 35, IF = 36, ELSE = 37, SWITCH = 38, 
               CASE = 39, DEFAULT = 40, FOR = 41, BREAK = 42, CONTINUE = 43, 
               RETURN = 44, TRUE = 45, FALSE = 46, NIL = 47, INT32_TYPE = 48, 
               FLOAT32_TYPE = 49, BOOL_TYPE = 50, RUNE_TYPE = 51, STRING_TYPE = 52, 
               AND = 53, OR = 54, FLOAT32 = 55, INT32 = 56, RUNE = 57, STRING = 58, 
               ID = 59, LINE_COMMENT = 60, BLOCK_COMMENT = 61, WS = 62;

		public const RULE_program = 0, RULE_declaration = 1, RULE_varDeclaration = 2, 
               RULE_shortVarDeclaration = 3, RULE_constDeclaration = 4, 
               RULE_functionDeclaration = 5, RULE_parameterList = 6, RULE_parameter = 7, 
               RULE_typeList = 8, RULE_idList = 9, RULE_expressionList = 10, 
               RULE_statement = 11, RULE_assignment = 12, RULE_assignOp = 13, 
               RULE_incDecStatement = 14, RULE_ifStatement = 15, RULE_switchStatement = 16, 
               RULE_caseClause = 17, RULE_defaultClause = 18, RULE_forStatement = 19, 
               RULE_forClause = 20, RULE_forInit = 21, RULE_forPost = 22, 
               RULE_breakStatement = 23, RULE_continueStatement = 24, RULE_returnStatement = 25, 
               RULE_block = 26, RULE_expressionStatement = 27, RULE_expression = 28, 
               RULE_logicalOr = 29, RULE_logicalAnd = 30, RULE_equality = 31, 
               RULE_relational = 32, RULE_additive = 33, RULE_multiplicative = 34, 
               RULE_unary = 35, RULE_primary = 36, RULE_arrayLiteral = 37, 
               RULE_innerLiteralList = 38, RULE_innerLiteral = 39, RULE_argumentList = 40, 
               RULE_argument = 41, RULE_type = 42;

		/**
		 * @var array<string>
		 */
		public const RULE_NAMES = [
			'program', 'declaration', 'varDeclaration', 'shortVarDeclaration', 'constDeclaration', 
			'functionDeclaration', 'parameterList', 'parameter', 'typeList', 'idList', 
			'expressionList', 'statement', 'assignment', 'assignOp', 'incDecStatement', 
			'ifStatement', 'switchStatement', 'caseClause', 'defaultClause', 'forStatement', 
			'forClause', 'forInit', 'forPost', 'breakStatement', 'continueStatement', 
			'returnStatement', 'block', 'expressionStatement', 'expression', 'logicalOr', 
			'logicalAnd', 'equality', 'relational', 'additive', 'multiplicative', 
			'unary', 'primary', 'arrayLiteral', 'innerLiteralList', 'innerLiteral', 
			'argumentList', 'argument', 'type'
		];

		/**
		 * @var array<string|null>
		 */
		private const LITERAL_NAMES = [
		    null, "'='", "':='", "'('", "')'", "','", "'*'", "'['", "']'", "'+='", 
		    "'-='", "'*='", "'/='", "'++'", "'--'", "'{'", "'}'", "':'", "';'", 
		    "'?'", "'=='", "'!='", "'>'", "'>='", "'<'", "'<='", "'+'", "'-'", 
		    "'/'", "'%'", "'!'", "'&'", "'.'", "'var'", "'const'", "'func'", "'if'", 
		    "'else'", "'switch'", "'case'", "'default'", "'for'", "'break'", "'continue'", 
		    "'return'", "'true'", "'false'", "'nil'", "'int32'", "'float32'", 
		    "'bool'", "'rune'", "'string'", "'&&'", "'||'"
		];

		/**
		 * @var array<string>
		 */
		private const SYMBOLIC_NAMES = [
		    null, null, null, null, null, null, null, null, null, null, null, 
		    null, null, null, null, null, null, null, null, null, null, null, 
		    null, null, null, null, null, null, null, null, null, null, null, 
		    "VAR", "CONST", "FUNC", "IF", "ELSE", "SWITCH", "CASE", "DEFAULT", 
		    "FOR", "BREAK", "CONTINUE", "RETURN", "TRUE", "FALSE", "NIL", "INT32_TYPE", 
		    "FLOAT32_TYPE", "BOOL_TYPE", "RUNE_TYPE", "STRING_TYPE", "AND", "OR", 
		    "FLOAT32", "INT32", "RUNE", "STRING", "ID", "LINE_COMMENT", "BLOCK_COMMENT", 
		    "WS"
		];

		private const SERIALIZED_ATN =
			[4, 1, 62, 506, 2, 0, 7, 0, 2, 1, 7, 1, 2, 2, 7, 2, 2, 3, 7, 3, 2, 4, 
		    7, 4, 2, 5, 7, 5, 2, 6, 7, 6, 2, 7, 7, 7, 2, 8, 7, 8, 2, 9, 7, 9, 
		    2, 10, 7, 10, 2, 11, 7, 11, 2, 12, 7, 12, 2, 13, 7, 13, 2, 14, 7, 
		    14, 2, 15, 7, 15, 2, 16, 7, 16, 2, 17, 7, 17, 2, 18, 7, 18, 2, 19, 
		    7, 19, 2, 20, 7, 20, 2, 21, 7, 21, 2, 22, 7, 22, 2, 23, 7, 23, 2, 
		    24, 7, 24, 2, 25, 7, 25, 2, 26, 7, 26, 2, 27, 7, 27, 2, 28, 7, 28, 
		    2, 29, 7, 29, 2, 30, 7, 30, 2, 31, 7, 31, 2, 32, 7, 32, 2, 33, 7, 
		    33, 2, 34, 7, 34, 2, 35, 7, 35, 2, 36, 7, 36, 2, 37, 7, 37, 2, 38, 
		    7, 38, 2, 39, 7, 39, 2, 40, 7, 40, 2, 41, 7, 41, 2, 42, 7, 42, 1, 
		    0, 5, 0, 88, 8, 0, 10, 0, 12, 0, 91, 9, 0, 1, 0, 1, 0, 1, 1, 1, 1, 
		    1, 1, 1, 1, 3, 1, 99, 8, 1, 1, 2, 1, 2, 1, 2, 1, 2, 1, 2, 1, 2, 1, 
		    2, 1, 2, 1, 2, 1, 2, 3, 2, 111, 8, 2, 1, 3, 1, 3, 1, 3, 1, 3, 1, 4, 
		    1, 4, 1, 4, 1, 4, 1, 4, 1, 4, 1, 5, 1, 5, 1, 5, 1, 5, 3, 5, 127, 8, 
		    5, 1, 5, 1, 5, 3, 5, 131, 8, 5, 1, 5, 1, 5, 1, 5, 1, 5, 1, 5, 3, 5, 
		    138, 8, 5, 1, 5, 1, 5, 1, 5, 1, 5, 1, 5, 1, 5, 3, 5, 146, 8, 5, 1, 
		    6, 1, 6, 1, 6, 5, 6, 151, 8, 6, 10, 6, 12, 6, 154, 9, 6, 1, 7, 1, 
		    7, 1, 7, 1, 7, 1, 7, 3, 7, 161, 8, 7, 1, 8, 1, 8, 1, 8, 5, 8, 166, 
		    8, 8, 10, 8, 12, 8, 169, 9, 8, 1, 9, 1, 9, 1, 9, 5, 9, 174, 8, 9, 
		    10, 9, 12, 9, 177, 9, 9, 1, 10, 1, 10, 1, 10, 5, 10, 182, 8, 10, 10, 
		    10, 12, 10, 185, 9, 10, 1, 11, 1, 11, 1, 11, 1, 11, 1, 11, 1, 11, 
		    1, 11, 1, 11, 1, 11, 1, 11, 1, 11, 3, 11, 198, 8, 11, 1, 12, 1, 12, 
		    1, 12, 1, 12, 1, 12, 1, 12, 1, 12, 1, 12, 1, 12, 4, 12, 209, 8, 12, 
		    11, 12, 12, 12, 210, 1, 12, 1, 12, 1, 12, 1, 12, 1, 12, 1, 12, 1, 
		    12, 1, 12, 3, 12, 221, 8, 12, 1, 13, 1, 13, 1, 14, 1, 14, 1, 14, 1, 
		    14, 3, 14, 229, 8, 14, 1, 15, 1, 15, 1, 15, 1, 15, 1, 15, 1, 15, 1, 
		    15, 1, 15, 5, 15, 239, 8, 15, 10, 15, 12, 15, 242, 9, 15, 1, 15, 1, 
		    15, 3, 15, 246, 8, 15, 1, 16, 1, 16, 1, 16, 1, 16, 5, 16, 252, 8, 
		    16, 10, 16, 12, 16, 255, 9, 16, 1, 16, 3, 16, 258, 8, 16, 1, 16, 1, 
		    16, 1, 17, 1, 17, 1, 17, 1, 17, 5, 17, 266, 8, 17, 10, 17, 12, 17, 
		    269, 9, 17, 1, 18, 1, 18, 1, 18, 5, 18, 274, 8, 18, 10, 18, 12, 18, 
		    277, 9, 18, 1, 19, 1, 19, 1, 19, 1, 19, 1, 19, 1, 19, 1, 19, 1, 19, 
		    1, 19, 1, 19, 3, 19, 289, 8, 19, 1, 20, 1, 20, 1, 20, 3, 20, 294, 
		    8, 20, 1, 20, 1, 20, 1, 20, 1, 21, 1, 21, 1, 21, 1, 21, 1, 21, 3, 
		    21, 304, 8, 21, 1, 22, 1, 22, 1, 22, 3, 22, 309, 8, 22, 1, 23, 1, 
		    23, 1, 24, 1, 24, 1, 25, 1, 25, 3, 25, 317, 8, 25, 1, 26, 1, 26, 5, 
		    26, 321, 8, 26, 10, 26, 12, 26, 324, 9, 26, 1, 26, 1, 26, 1, 27, 1, 
		    27, 1, 28, 1, 28, 1, 28, 1, 28, 1, 28, 1, 28, 1, 28, 3, 28, 337, 8, 
		    28, 1, 29, 1, 29, 1, 29, 5, 29, 342, 8, 29, 10, 29, 12, 29, 345, 9, 
		    29, 1, 30, 1, 30, 1, 30, 5, 30, 350, 8, 30, 10, 30, 12, 30, 353, 9, 
		    30, 1, 31, 1, 31, 1, 31, 5, 31, 358, 8, 31, 10, 31, 12, 31, 361, 9, 
		    31, 1, 32, 1, 32, 1, 32, 5, 32, 366, 8, 32, 10, 32, 12, 32, 369, 9, 
		    32, 1, 33, 1, 33, 1, 33, 5, 33, 374, 8, 33, 10, 33, 12, 33, 377, 9, 
		    33, 1, 34, 1, 34, 1, 34, 5, 34, 382, 8, 34, 10, 34, 12, 34, 385, 9, 
		    34, 1, 35, 1, 35, 1, 35, 1, 35, 1, 35, 1, 35, 1, 35, 1, 35, 1, 35, 
		    3, 35, 396, 8, 35, 1, 36, 1, 36, 1, 36, 1, 36, 1, 36, 1, 36, 1, 36, 
		    1, 36, 1, 36, 1, 36, 3, 36, 408, 8, 36, 1, 36, 1, 36, 3, 36, 412, 
		    8, 36, 1, 36, 1, 36, 1, 36, 1, 36, 1, 36, 1, 36, 4, 36, 420, 8, 36, 
		    11, 36, 12, 36, 421, 1, 36, 1, 36, 1, 36, 1, 36, 1, 36, 1, 36, 1, 
		    36, 1, 36, 1, 36, 1, 36, 3, 36, 434, 8, 36, 1, 37, 1, 37, 1, 37, 1, 
		    37, 1, 37, 1, 37, 1, 37, 3, 37, 443, 8, 37, 1, 37, 1, 37, 1, 37, 1, 
		    37, 1, 37, 1, 37, 1, 37, 3, 37, 452, 8, 37, 1, 37, 1, 37, 3, 37, 456, 
		    8, 37, 1, 38, 1, 38, 1, 38, 5, 38, 461, 8, 38, 10, 38, 12, 38, 464, 
		    9, 38, 1, 38, 3, 38, 467, 8, 38, 1, 39, 1, 39, 1, 39, 3, 39, 472, 
		    8, 39, 1, 39, 1, 39, 1, 40, 1, 40, 1, 40, 5, 40, 479, 8, 40, 10, 40, 
		    12, 40, 482, 9, 40, 1, 41, 1, 41, 1, 41, 3, 41, 487, 8, 41, 1, 42, 
		    1, 42, 1, 42, 1, 42, 1, 42, 1, 42, 1, 42, 1, 42, 1, 42, 1, 42, 1, 
		    42, 1, 42, 1, 42, 1, 42, 1, 42, 3, 42, 504, 8, 42, 1, 42, 0, 0, 43, 
		    0, 2, 4, 6, 8, 10, 12, 14, 16, 18, 20, 22, 24, 26, 28, 30, 32, 34, 
		    36, 38, 40, 42, 44, 46, 48, 50, 52, 54, 56, 58, 60, 62, 64, 66, 68, 
		    70, 72, 74, 76, 78, 80, 82, 84, 0, 5, 2, 0, 1, 1, 9, 12, 1, 0, 20, 
		    21, 1, 0, 22, 25, 1, 0, 26, 27, 2, 0, 6, 6, 28, 29, 549, 0, 89, 1, 
		    0, 0, 0, 2, 98, 1, 0, 0, 0, 4, 110, 1, 0, 0, 0, 6, 112, 1, 0, 0, 0, 
		    8, 116, 1, 0, 0, 0, 10, 145, 1, 0, 0, 0, 12, 147, 1, 0, 0, 0, 14, 
		    160, 1, 0, 0, 0, 16, 162, 1, 0, 0, 0, 18, 170, 1, 0, 0, 0, 20, 178, 
		    1, 0, 0, 0, 22, 197, 1, 0, 0, 0, 24, 220, 1, 0, 0, 0, 26, 222, 1, 
		    0, 0, 0, 28, 228, 1, 0, 0, 0, 30, 230, 1, 0, 0, 0, 32, 247, 1, 0, 
		    0, 0, 34, 261, 1, 0, 0, 0, 36, 270, 1, 0, 0, 0, 38, 288, 1, 0, 0, 
		    0, 40, 290, 1, 0, 0, 0, 42, 303, 1, 0, 0, 0, 44, 308, 1, 0, 0, 0, 
		    46, 310, 1, 0, 0, 0, 48, 312, 1, 0, 0, 0, 50, 314, 1, 0, 0, 0, 52, 
		    318, 1, 0, 0, 0, 54, 327, 1, 0, 0, 0, 56, 336, 1, 0, 0, 0, 58, 338, 
		    1, 0, 0, 0, 60, 346, 1, 0, 0, 0, 62, 354, 1, 0, 0, 0, 64, 362, 1, 
		    0, 0, 0, 66, 370, 1, 0, 0, 0, 68, 378, 1, 0, 0, 0, 70, 395, 1, 0, 
		    0, 0, 72, 433, 1, 0, 0, 0, 74, 455, 1, 0, 0, 0, 76, 457, 1, 0, 0, 
		    0, 78, 468, 1, 0, 0, 0, 80, 475, 1, 0, 0, 0, 82, 486, 1, 0, 0, 0, 
		    84, 503, 1, 0, 0, 0, 86, 88, 3, 2, 1, 0, 87, 86, 1, 0, 0, 0, 88, 91, 
		    1, 0, 0, 0, 89, 87, 1, 0, 0, 0, 89, 90, 1, 0, 0, 0, 90, 92, 1, 0, 
		    0, 0, 91, 89, 1, 0, 0, 0, 92, 93, 5, 0, 0, 1, 93, 1, 1, 0, 0, 0, 94, 
		    99, 3, 4, 2, 0, 95, 99, 3, 8, 4, 0, 96, 99, 3, 10, 5, 0, 97, 99, 3, 
		    22, 11, 0, 98, 94, 1, 0, 0, 0, 98, 95, 1, 0, 0, 0, 98, 96, 1, 0, 0, 
		    0, 98, 97, 1, 0, 0, 0, 99, 3, 1, 0, 0, 0, 100, 101, 5, 33, 0, 0, 101, 
		    102, 3, 18, 9, 0, 102, 103, 3, 84, 42, 0, 103, 111, 1, 0, 0, 0, 104, 
		    105, 5, 33, 0, 0, 105, 106, 3, 18, 9, 0, 106, 107, 3, 84, 42, 0, 107, 
		    108, 5, 1, 0, 0, 108, 109, 3, 20, 10, 0, 109, 111, 1, 0, 0, 0, 110, 
		    100, 1, 0, 0, 0, 110, 104, 1, 0, 0, 0, 111, 5, 1, 0, 0, 0, 112, 113, 
		    3, 18, 9, 0, 113, 114, 5, 2, 0, 0, 114, 115, 3, 20, 10, 0, 115, 7, 
		    1, 0, 0, 0, 116, 117, 5, 34, 0, 0, 117, 118, 5, 59, 0, 0, 118, 119, 
		    3, 84, 42, 0, 119, 120, 5, 1, 0, 0, 120, 121, 3, 56, 28, 0, 121, 9, 
		    1, 0, 0, 0, 122, 123, 5, 35, 0, 0, 123, 124, 5, 59, 0, 0, 124, 126, 
		    5, 3, 0, 0, 125, 127, 3, 12, 6, 0, 126, 125, 1, 0, 0, 0, 126, 127, 
		    1, 0, 0, 0, 127, 128, 1, 0, 0, 0, 128, 130, 5, 4, 0, 0, 129, 131, 
		    3, 84, 42, 0, 130, 129, 1, 0, 0, 0, 130, 131, 1, 0, 0, 0, 131, 132, 
		    1, 0, 0, 0, 132, 146, 3, 52, 26, 0, 133, 134, 5, 35, 0, 0, 134, 135, 
		    5, 59, 0, 0, 135, 137, 5, 3, 0, 0, 136, 138, 3, 12, 6, 0, 137, 136, 
		    1, 0, 0, 0, 137, 138, 1, 0, 0, 0, 138, 139, 1, 0, 0, 0, 139, 140, 
		    5, 4, 0, 0, 140, 141, 5, 3, 0, 0, 141, 142, 3, 16, 8, 0, 142, 143, 
		    5, 4, 0, 0, 143, 144, 3, 52, 26, 0, 144, 146, 1, 0, 0, 0, 145, 122, 
		    1, 0, 0, 0, 145, 133, 1, 0, 0, 0, 146, 11, 1, 0, 0, 0, 147, 152, 3, 
		    14, 7, 0, 148, 149, 5, 5, 0, 0, 149, 151, 3, 14, 7, 0, 150, 148, 1, 
		    0, 0, 0, 151, 154, 1, 0, 0, 0, 152, 150, 1, 0, 0, 0, 152, 153, 1, 
		    0, 0, 0, 153, 13, 1, 0, 0, 0, 154, 152, 1, 0, 0, 0, 155, 156, 5, 59, 
		    0, 0, 156, 161, 3, 84, 42, 0, 157, 158, 5, 6, 0, 0, 158, 159, 5, 59, 
		    0, 0, 159, 161, 3, 84, 42, 0, 160, 155, 1, 0, 0, 0, 160, 157, 1, 0, 
		    0, 0, 161, 15, 1, 0, 0, 0, 162, 167, 3, 84, 42, 0, 163, 164, 5, 5, 
		    0, 0, 164, 166, 3, 84, 42, 0, 165, 163, 1, 0, 0, 0, 166, 169, 1, 0, 
		    0, 0, 167, 165, 1, 0, 0, 0, 167, 168, 1, 0, 0, 0, 168, 17, 1, 0, 0, 
		    0, 169, 167, 1, 0, 0, 0, 170, 175, 5, 59, 0, 0, 171, 172, 5, 5, 0, 
		    0, 172, 174, 5, 59, 0, 0, 173, 171, 1, 0, 0, 0, 174, 177, 1, 0, 0, 
		    0, 175, 173, 1, 0, 0, 0, 175, 176, 1, 0, 0, 0, 176, 19, 1, 0, 0, 0, 
		    177, 175, 1, 0, 0, 0, 178, 183, 3, 56, 28, 0, 179, 180, 5, 5, 0, 0, 
		    180, 182, 3, 56, 28, 0, 181, 179, 1, 0, 0, 0, 182, 185, 1, 0, 0, 0, 
		    183, 181, 1, 0, 0, 0, 183, 184, 1, 0, 0, 0, 184, 21, 1, 0, 0, 0, 185, 
		    183, 1, 0, 0, 0, 186, 198, 3, 6, 3, 0, 187, 198, 3, 24, 12, 0, 188, 
		    198, 3, 30, 15, 0, 189, 198, 3, 32, 16, 0, 190, 198, 3, 38, 19, 0, 
		    191, 198, 3, 46, 23, 0, 192, 198, 3, 48, 24, 0, 193, 198, 3, 50, 25, 
		    0, 194, 198, 3, 28, 14, 0, 195, 198, 3, 52, 26, 0, 196, 198, 3, 54, 
		    27, 0, 197, 186, 1, 0, 0, 0, 197, 187, 1, 0, 0, 0, 197, 188, 1, 0, 
		    0, 0, 197, 189, 1, 0, 0, 0, 197, 190, 1, 0, 0, 0, 197, 191, 1, 0, 
		    0, 0, 197, 192, 1, 0, 0, 0, 197, 193, 1, 0, 0, 0, 197, 194, 1, 0, 
		    0, 0, 197, 195, 1, 0, 0, 0, 197, 196, 1, 0, 0, 0, 198, 23, 1, 0, 0, 
		    0, 199, 200, 5, 59, 0, 0, 200, 201, 3, 26, 13, 0, 201, 202, 3, 56, 
		    28, 0, 202, 221, 1, 0, 0, 0, 203, 208, 5, 59, 0, 0, 204, 205, 5, 7, 
		    0, 0, 205, 206, 3, 56, 28, 0, 206, 207, 5, 8, 0, 0, 207, 209, 1, 0, 
		    0, 0, 208, 204, 1, 0, 0, 0, 209, 210, 1, 0, 0, 0, 210, 208, 1, 0, 
		    0, 0, 210, 211, 1, 0, 0, 0, 211, 212, 1, 0, 0, 0, 212, 213, 3, 26, 
		    13, 0, 213, 214, 3, 56, 28, 0, 214, 221, 1, 0, 0, 0, 215, 216, 5, 
		    6, 0, 0, 216, 217, 5, 59, 0, 0, 217, 218, 3, 26, 13, 0, 218, 219, 
		    3, 56, 28, 0, 219, 221, 1, 0, 0, 0, 220, 199, 1, 0, 0, 0, 220, 203, 
		    1, 0, 0, 0, 220, 215, 1, 0, 0, 0, 221, 25, 1, 0, 0, 0, 222, 223, 7, 
		    0, 0, 0, 223, 27, 1, 0, 0, 0, 224, 225, 5, 59, 0, 0, 225, 229, 5, 
		    13, 0, 0, 226, 227, 5, 59, 0, 0, 227, 229, 5, 14, 0, 0, 228, 224, 
		    1, 0, 0, 0, 228, 226, 1, 0, 0, 0, 229, 29, 1, 0, 0, 0, 230, 231, 5, 
		    36, 0, 0, 231, 232, 3, 56, 28, 0, 232, 240, 3, 52, 26, 0, 233, 234, 
		    5, 37, 0, 0, 234, 235, 5, 36, 0, 0, 235, 236, 3, 56, 28, 0, 236, 237, 
		    3, 52, 26, 0, 237, 239, 1, 0, 0, 0, 238, 233, 1, 0, 0, 0, 239, 242, 
		    1, 0, 0, 0, 240, 238, 1, 0, 0, 0, 240, 241, 1, 0, 0, 0, 241, 245, 
		    1, 0, 0, 0, 242, 240, 1, 0, 0, 0, 243, 244, 5, 37, 0, 0, 244, 246, 
		    3, 52, 26, 0, 245, 243, 1, 0, 0, 0, 245, 246, 1, 0, 0, 0, 246, 31, 
		    1, 0, 0, 0, 247, 248, 5, 38, 0, 0, 248, 249, 3, 56, 28, 0, 249, 253, 
		    5, 15, 0, 0, 250, 252, 3, 34, 17, 0, 251, 250, 1, 0, 0, 0, 252, 255, 
		    1, 0, 0, 0, 253, 251, 1, 0, 0, 0, 253, 254, 1, 0, 0, 0, 254, 257, 
		    1, 0, 0, 0, 255, 253, 1, 0, 0, 0, 256, 258, 3, 36, 18, 0, 257, 256, 
		    1, 0, 0, 0, 257, 258, 1, 0, 0, 0, 258, 259, 1, 0, 0, 0, 259, 260, 
		    5, 16, 0, 0, 260, 33, 1, 0, 0, 0, 261, 262, 5, 39, 0, 0, 262, 263, 
		    3, 20, 10, 0, 263, 267, 5, 17, 0, 0, 264, 266, 3, 22, 11, 0, 265, 
		    264, 1, 0, 0, 0, 266, 269, 1, 0, 0, 0, 267, 265, 1, 0, 0, 0, 267, 
		    268, 1, 0, 0, 0, 268, 35, 1, 0, 0, 0, 269, 267, 1, 0, 0, 0, 270, 271, 
		    5, 40, 0, 0, 271, 275, 5, 17, 0, 0, 272, 274, 3, 22, 11, 0, 273, 272, 
		    1, 0, 0, 0, 274, 277, 1, 0, 0, 0, 275, 273, 1, 0, 0, 0, 275, 276, 
		    1, 0, 0, 0, 276, 37, 1, 0, 0, 0, 277, 275, 1, 0, 0, 0, 278, 279, 5, 
		    41, 0, 0, 279, 280, 3, 40, 20, 0, 280, 281, 3, 52, 26, 0, 281, 289, 
		    1, 0, 0, 0, 282, 283, 5, 41, 0, 0, 283, 284, 3, 56, 28, 0, 284, 285, 
		    3, 52, 26, 0, 285, 289, 1, 0, 0, 0, 286, 287, 5, 41, 0, 0, 287, 289, 
		    3, 52, 26, 0, 288, 278, 1, 0, 0, 0, 288, 282, 1, 0, 0, 0, 288, 286, 
		    1, 0, 0, 0, 289, 39, 1, 0, 0, 0, 290, 291, 3, 42, 21, 0, 291, 293, 
		    5, 18, 0, 0, 292, 294, 3, 56, 28, 0, 293, 292, 1, 0, 0, 0, 293, 294, 
		    1, 0, 0, 0, 294, 295, 1, 0, 0, 0, 295, 296, 5, 18, 0, 0, 296, 297, 
		    3, 44, 22, 0, 297, 41, 1, 0, 0, 0, 298, 304, 3, 4, 2, 0, 299, 304, 
		    3, 6, 3, 0, 300, 304, 3, 24, 12, 0, 301, 304, 3, 28, 14, 0, 302, 304, 
		    1, 0, 0, 0, 303, 298, 1, 0, 0, 0, 303, 299, 1, 0, 0, 0, 303, 300, 
		    1, 0, 0, 0, 303, 301, 1, 0, 0, 0, 303, 302, 1, 0, 0, 0, 304, 43, 1, 
		    0, 0, 0, 305, 309, 3, 24, 12, 0, 306, 309, 3, 28, 14, 0, 307, 309, 
		    1, 0, 0, 0, 308, 305, 1, 0, 0, 0, 308, 306, 1, 0, 0, 0, 308, 307, 
		    1, 0, 0, 0, 309, 45, 1, 0, 0, 0, 310, 311, 5, 42, 0, 0, 311, 47, 1, 
		    0, 0, 0, 312, 313, 5, 43, 0, 0, 313, 49, 1, 0, 0, 0, 314, 316, 5, 
		    44, 0, 0, 315, 317, 3, 20, 10, 0, 316, 315, 1, 0, 0, 0, 316, 317, 
		    1, 0, 0, 0, 317, 51, 1, 0, 0, 0, 318, 322, 5, 15, 0, 0, 319, 321, 
		    3, 2, 1, 0, 320, 319, 1, 0, 0, 0, 321, 324, 1, 0, 0, 0, 322, 320, 
		    1, 0, 0, 0, 322, 323, 1, 0, 0, 0, 323, 325, 1, 0, 0, 0, 324, 322, 
		    1, 0, 0, 0, 325, 326, 5, 16, 0, 0, 326, 53, 1, 0, 0, 0, 327, 328, 
		    3, 56, 28, 0, 328, 55, 1, 0, 0, 0, 329, 330, 3, 58, 29, 0, 330, 331, 
		    5, 19, 0, 0, 331, 332, 3, 56, 28, 0, 332, 333, 5, 17, 0, 0, 333, 334, 
		    3, 56, 28, 0, 334, 337, 1, 0, 0, 0, 335, 337, 3, 58, 29, 0, 336, 329, 
		    1, 0, 0, 0, 336, 335, 1, 0, 0, 0, 337, 57, 1, 0, 0, 0, 338, 343, 3, 
		    60, 30, 0, 339, 340, 5, 54, 0, 0, 340, 342, 3, 60, 30, 0, 341, 339, 
		    1, 0, 0, 0, 342, 345, 1, 0, 0, 0, 343, 341, 1, 0, 0, 0, 343, 344, 
		    1, 0, 0, 0, 344, 59, 1, 0, 0, 0, 345, 343, 1, 0, 0, 0, 346, 351, 3, 
		    62, 31, 0, 347, 348, 5, 53, 0, 0, 348, 350, 3, 62, 31, 0, 349, 347, 
		    1, 0, 0, 0, 350, 353, 1, 0, 0, 0, 351, 349, 1, 0, 0, 0, 351, 352, 
		    1, 0, 0, 0, 352, 61, 1, 0, 0, 0, 353, 351, 1, 0, 0, 0, 354, 359, 3, 
		    64, 32, 0, 355, 356, 7, 1, 0, 0, 356, 358, 3, 64, 32, 0, 357, 355, 
		    1, 0, 0, 0, 358, 361, 1, 0, 0, 0, 359, 357, 1, 0, 0, 0, 359, 360, 
		    1, 0, 0, 0, 360, 63, 1, 0, 0, 0, 361, 359, 1, 0, 0, 0, 362, 367, 3, 
		    66, 33, 0, 363, 364, 7, 2, 0, 0, 364, 366, 3, 66, 33, 0, 365, 363, 
		    1, 0, 0, 0, 366, 369, 1, 0, 0, 0, 367, 365, 1, 0, 0, 0, 367, 368, 
		    1, 0, 0, 0, 368, 65, 1, 0, 0, 0, 369, 367, 1, 0, 0, 0, 370, 375, 3, 
		    68, 34, 0, 371, 372, 7, 3, 0, 0, 372, 374, 3, 68, 34, 0, 373, 371, 
		    1, 0, 0, 0, 374, 377, 1, 0, 0, 0, 375, 373, 1, 0, 0, 0, 375, 376, 
		    1, 0, 0, 0, 376, 67, 1, 0, 0, 0, 377, 375, 1, 0, 0, 0, 378, 383, 3, 
		    70, 35, 0, 379, 380, 7, 4, 0, 0, 380, 382, 3, 70, 35, 0, 381, 379, 
		    1, 0, 0, 0, 382, 385, 1, 0, 0, 0, 383, 381, 1, 0, 0, 0, 383, 384, 
		    1, 0, 0, 0, 384, 69, 1, 0, 0, 0, 385, 383, 1, 0, 0, 0, 386, 396, 3, 
		    72, 36, 0, 387, 388, 5, 27, 0, 0, 388, 396, 3, 70, 35, 0, 389, 390, 
		    5, 30, 0, 0, 390, 396, 3, 70, 35, 0, 391, 392, 5, 31, 0, 0, 392, 396, 
		    5, 59, 0, 0, 393, 394, 5, 6, 0, 0, 394, 396, 3, 70, 35, 0, 395, 386, 
		    1, 0, 0, 0, 395, 387, 1, 0, 0, 0, 395, 389, 1, 0, 0, 0, 395, 391, 
		    1, 0, 0, 0, 395, 393, 1, 0, 0, 0, 396, 71, 1, 0, 0, 0, 397, 434, 5, 
		    56, 0, 0, 398, 434, 5, 55, 0, 0, 399, 434, 5, 57, 0, 0, 400, 434, 
		    5, 58, 0, 0, 401, 434, 5, 45, 0, 0, 402, 434, 5, 46, 0, 0, 403, 434, 
		    5, 47, 0, 0, 404, 407, 5, 59, 0, 0, 405, 406, 5, 32, 0, 0, 406, 408, 
		    5, 59, 0, 0, 407, 405, 1, 0, 0, 0, 407, 408, 1, 0, 0, 0, 408, 409, 
		    1, 0, 0, 0, 409, 411, 5, 3, 0, 0, 410, 412, 3, 80, 40, 0, 411, 410, 
		    1, 0, 0, 0, 411, 412, 1, 0, 0, 0, 412, 413, 1, 0, 0, 0, 413, 434, 
		    5, 4, 0, 0, 414, 419, 5, 59, 0, 0, 415, 416, 5, 7, 0, 0, 416, 417, 
		    3, 56, 28, 0, 417, 418, 5, 8, 0, 0, 418, 420, 1, 0, 0, 0, 419, 415, 
		    1, 0, 0, 0, 420, 421, 1, 0, 0, 0, 421, 419, 1, 0, 0, 0, 421, 422, 
		    1, 0, 0, 0, 422, 434, 1, 0, 0, 0, 423, 434, 5, 59, 0, 0, 424, 425, 
		    5, 3, 0, 0, 425, 426, 3, 56, 28, 0, 426, 427, 5, 4, 0, 0, 427, 434, 
		    1, 0, 0, 0, 428, 434, 3, 74, 37, 0, 429, 430, 5, 15, 0, 0, 430, 431, 
		    3, 20, 10, 0, 431, 432, 5, 16, 0, 0, 432, 434, 1, 0, 0, 0, 433, 397, 
		    1, 0, 0, 0, 433, 398, 1, 0, 0, 0, 433, 399, 1, 0, 0, 0, 433, 400, 
		    1, 0, 0, 0, 433, 401, 1, 0, 0, 0, 433, 402, 1, 0, 0, 0, 433, 403, 
		    1, 0, 0, 0, 433, 404, 1, 0, 0, 0, 433, 414, 1, 0, 0, 0, 433, 423, 
		    1, 0, 0, 0, 433, 424, 1, 0, 0, 0, 433, 428, 1, 0, 0, 0, 433, 429, 
		    1, 0, 0, 0, 434, 73, 1, 0, 0, 0, 435, 436, 5, 7, 0, 0, 436, 437, 3, 
		    56, 28, 0, 437, 438, 5, 8, 0, 0, 438, 439, 3, 84, 42, 0, 439, 442, 
		    5, 15, 0, 0, 440, 443, 3, 20, 10, 0, 441, 443, 3, 76, 38, 0, 442, 
		    440, 1, 0, 0, 0, 442, 441, 1, 0, 0, 0, 442, 443, 1, 0, 0, 0, 443, 
		    444, 1, 0, 0, 0, 444, 445, 5, 16, 0, 0, 445, 456, 1, 0, 0, 0, 446, 
		    447, 5, 7, 0, 0, 447, 448, 5, 8, 0, 0, 448, 449, 3, 84, 42, 0, 449, 
		    451, 5, 15, 0, 0, 450, 452, 3, 20, 10, 0, 451, 450, 1, 0, 0, 0, 451, 
		    452, 1, 0, 0, 0, 452, 453, 1, 0, 0, 0, 453, 454, 5, 16, 0, 0, 454, 
		    456, 1, 0, 0, 0, 455, 435, 1, 0, 0, 0, 455, 446, 1, 0, 0, 0, 456, 
		    75, 1, 0, 0, 0, 457, 462, 3, 78, 39, 0, 458, 459, 5, 5, 0, 0, 459, 
		    461, 3, 78, 39, 0, 460, 458, 1, 0, 0, 0, 461, 464, 1, 0, 0, 0, 462, 
		    460, 1, 0, 0, 0, 462, 463, 1, 0, 0, 0, 463, 466, 1, 0, 0, 0, 464, 
		    462, 1, 0, 0, 0, 465, 467, 5, 5, 0, 0, 466, 465, 1, 0, 0, 0, 466, 
		    467, 1, 0, 0, 0, 467, 77, 1, 0, 0, 0, 468, 469, 5, 15, 0, 0, 469, 
		    471, 3, 20, 10, 0, 470, 472, 5, 5, 0, 0, 471, 470, 1, 0, 0, 0, 471, 
		    472, 1, 0, 0, 0, 472, 473, 1, 0, 0, 0, 473, 474, 5, 16, 0, 0, 474, 
		    79, 1, 0, 0, 0, 475, 480, 3, 82, 41, 0, 476, 477, 5, 5, 0, 0, 477, 
		    479, 3, 82, 41, 0, 478, 476, 1, 0, 0, 0, 479, 482, 1, 0, 0, 0, 480, 
		    478, 1, 0, 0, 0, 480, 481, 1, 0, 0, 0, 481, 81, 1, 0, 0, 0, 482, 480, 
		    1, 0, 0, 0, 483, 487, 3, 56, 28, 0, 484, 485, 5, 31, 0, 0, 485, 487, 
		    5, 59, 0, 0, 486, 483, 1, 0, 0, 0, 486, 484, 1, 0, 0, 0, 487, 83, 
		    1, 0, 0, 0, 488, 504, 5, 48, 0, 0, 489, 504, 5, 49, 0, 0, 490, 504, 
		    5, 50, 0, 0, 491, 504, 5, 51, 0, 0, 492, 504, 5, 52, 0, 0, 493, 494, 
		    5, 7, 0, 0, 494, 495, 3, 56, 28, 0, 495, 496, 5, 8, 0, 0, 496, 497, 
		    3, 84, 42, 0, 497, 504, 1, 0, 0, 0, 498, 499, 5, 7, 0, 0, 499, 500, 
		    5, 8, 0, 0, 500, 504, 3, 84, 42, 0, 501, 502, 5, 6, 0, 0, 502, 504, 
		    3, 84, 42, 0, 503, 488, 1, 0, 0, 0, 503, 489, 1, 0, 0, 0, 503, 490, 
		    1, 0, 0, 0, 503, 491, 1, 0, 0, 0, 503, 492, 1, 0, 0, 0, 503, 493, 
		    1, 0, 0, 0, 503, 498, 1, 0, 0, 0, 503, 501, 1, 0, 0, 0, 504, 85, 1, 
		    0, 0, 0, 49, 89, 98, 110, 126, 130, 137, 145, 152, 160, 167, 175, 
		    183, 197, 210, 220, 228, 240, 245, 253, 257, 267, 275, 288, 293, 303, 
		    308, 316, 322, 336, 343, 351, 359, 367, 375, 383, 395, 407, 411, 421, 
		    433, 442, 451, 455, 462, 466, 471, 480, 486, 503];
		protected static $atn;
		protected static $decisionToDFA;
		protected static $sharedContextCache;

		public function __construct(TokenStream $input)
		{
			parent::__construct($input);

			self::initialize();

			$this->interp = new ParserATNSimulator($this, self::$atn, self::$decisionToDFA, self::$sharedContextCache);
		}

		private static function initialize(): void
		{
			if (self::$atn !== null) {
				return;
			}

			RuntimeMetaData::checkVersion('4.13.0', RuntimeMetaData::VERSION);

			$atn = (new ATNDeserializer())->deserialize(self::SERIALIZED_ATN);

			$decisionToDFA = [];
			for ($i = 0, $count = $atn->getNumberOfDecisions(); $i < $count; $i++) {
				$decisionToDFA[] = new DFA($atn->getDecisionState($i), $i);
			}

			self::$atn = $atn;
			self::$decisionToDFA = $decisionToDFA;
			self::$sharedContextCache = new PredictionContextCache();
		}

		public function getGrammarFileName(): string
		{
			return "Golampi.g4";
		}

		public function getRuleNames(): array
		{
			return self::RULE_NAMES;
		}

		public function getSerializedATN(): array
		{
			return self::SERIALIZED_ATN;
		}

		public function getATN(): ATN
		{
			return self::$atn;
		}

		public function getVocabulary(): Vocabulary
        {
            static $vocabulary;

			return $vocabulary = $vocabulary ?? new VocabularyImpl(self::LITERAL_NAMES, self::SYMBOLIC_NAMES);
        }

		/**
		 * @throws RecognitionException
		 */
		public function program(): Context\ProgramContext
		{
		    $localContext = new Context\ProgramContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 0, self::RULE_program);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(89);
		        $this->errorHandler->sync($this);

		        $_la = $this->input->LA(1);
		        while (((($_la) & ~0x3f) === 0 && ((1 << $_la) & 1117172390623740104) !== 0)) {
		        	$this->setState(86);
		        	$this->declaration();
		        	$this->setState(91);
		        	$this->errorHandler->sync($this);
		        	$_la = $this->input->LA(1);
		        }
		        $this->setState(92);
		        $this->match(self::EOF);
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function declaration(): Context\DeclarationContext
		{
		    $localContext = new Context\DeclarationContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 2, self::RULE_declaration);

		    try {
		        $this->setState(98);
		        $this->errorHandler->sync($this);

		        switch ($this->input->LA(1)) {
		            case self::VAR:
		            	$this->enterOuterAlt($localContext, 1);
		            	$this->setState(94);
		            	$this->varDeclaration();
		            	break;

		            case self::CONST:
		            	$this->enterOuterAlt($localContext, 2);
		            	$this->setState(95);
		            	$this->constDeclaration();
		            	break;

		            case self::FUNC:
		            	$this->enterOuterAlt($localContext, 3);
		            	$this->setState(96);
		            	$this->functionDeclaration();
		            	break;

		            case self::T__2:
		            case self::T__5:
		            case self::T__6:
		            case self::T__14:
		            case self::T__26:
		            case self::T__29:
		            case self::T__30:
		            case self::IF:
		            case self::SWITCH:
		            case self::FOR:
		            case self::BREAK:
		            case self::CONTINUE:
		            case self::RETURN:
		            case self::TRUE:
		            case self::FALSE:
		            case self::NIL:
		            case self::FLOAT32:
		            case self::INT32:
		            case self::RUNE:
		            case self::STRING:
		            case self::ID:
		            	$this->enterOuterAlt($localContext, 4);
		            	$this->setState(97);
		            	$this->statement();
		            	break;

		        default:
		        	throw new NoViableAltException($this);
		        }
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function varDeclaration(): Context\VarDeclarationContext
		{
		    $localContext = new Context\VarDeclarationContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 4, self::RULE_varDeclaration);

		    try {
		        $this->setState(110);
		        $this->errorHandler->sync($this);

		        switch ($this->getInterpreter()->adaptivePredict($this->input, 2, $this->ctx)) {
		        	case 1:
		        	    $localContext = new Context\VarDeclSimpleContext($localContext);
		        	    $this->enterOuterAlt($localContext, 1);
		        	    $this->setState(100);
		        	    $this->match(self::VAR);
		        	    $this->setState(101);
		        	    $this->idList();
		        	    $this->setState(102);
		        	    $this->type();
		        	break;

		        	case 2:
		        	    $localContext = new Context\VarDeclWithInitContext($localContext);
		        	    $this->enterOuterAlt($localContext, 2);
		        	    $this->setState(104);
		        	    $this->match(self::VAR);
		        	    $this->setState(105);
		        	    $this->idList();
		        	    $this->setState(106);
		        	    $this->type();
		        	    $this->setState(107);
		        	    $this->match(self::T__0);
		        	    $this->setState(108);
		        	    $this->expressionList();
		        	break;
		        }
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function shortVarDeclaration(): Context\ShortVarDeclarationContext
		{
		    $localContext = new Context\ShortVarDeclarationContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 6, self::RULE_shortVarDeclaration);

		    try {
		        $localContext = new Context\ShortVarDeclContext($localContext);
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(112);
		        $this->idList();
		        $this->setState(113);
		        $this->match(self::T__1);
		        $this->setState(114);
		        $this->expressionList();
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function constDeclaration(): Context\ConstDeclarationContext
		{
		    $localContext = new Context\ConstDeclarationContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 8, self::RULE_constDeclaration);

		    try {
		        $localContext = new Context\ConstDeclContext($localContext);
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(116);
		        $this->match(self::CONST);
		        $this->setState(117);
		        $this->match(self::ID);
		        $this->setState(118);
		        $this->type();
		        $this->setState(119);
		        $this->match(self::T__0);
		        $this->setState(120);
		        $this->expression();
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function functionDeclaration(): Context\FunctionDeclarationContext
		{
		    $localContext = new Context\FunctionDeclarationContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 10, self::RULE_functionDeclaration);

		    try {
		        $this->setState(145);
		        $this->errorHandler->sync($this);

		        switch ($this->getInterpreter()->adaptivePredict($this->input, 6, $this->ctx)) {
		        	case 1:
		        	    $localContext = new Context\FuncDeclSingleReturnContext($localContext);
		        	    $this->enterOuterAlt($localContext, 1);
		        	    $this->setState(122);
		        	    $this->match(self::FUNC);
		        	    $this->setState(123);
		        	    $this->match(self::ID);
		        	    $this->setState(124);
		        	    $this->match(self::T__2);
		        	    $this->setState(126);
		        	    $this->errorHandler->sync($this);
		        	    $_la = $this->input->LA(1);

		        	    if ($_la === self::T__5 || $_la === self::ID) {
		        	    	$this->setState(125);
		        	    	$this->parameterList();
		        	    }
		        	    $this->setState(128);
		        	    $this->match(self::T__3);
		        	    $this->setState(130);
		        	    $this->errorHandler->sync($this);
		        	    $_la = $this->input->LA(1);

		        	    if (((($_la) & ~0x3f) === 0 && ((1 << $_la) & 8725724278030528) !== 0)) {
		        	    	$this->setState(129);
		        	    	$this->type();
		        	    }
		        	    $this->setState(132);
		        	    $this->block();
		        	break;

		        	case 2:
		        	    $localContext = new Context\FuncDeclMultiReturnContext($localContext);
		        	    $this->enterOuterAlt($localContext, 2);
		        	    $this->setState(133);
		        	    $this->match(self::FUNC);
		        	    $this->setState(134);
		        	    $this->match(self::ID);
		        	    $this->setState(135);
		        	    $this->match(self::T__2);
		        	    $this->setState(137);
		        	    $this->errorHandler->sync($this);
		        	    $_la = $this->input->LA(1);

		        	    if ($_la === self::T__5 || $_la === self::ID) {
		        	    	$this->setState(136);
		        	    	$this->parameterList();
		        	    }
		        	    $this->setState(139);
		        	    $this->match(self::T__3);
		        	    $this->setState(140);
		        	    $this->match(self::T__2);
		        	    $this->setState(141);
		        	    $this->typeList();
		        	    $this->setState(142);
		        	    $this->match(self::T__3);
		        	    $this->setState(143);
		        	    $this->block();
		        	break;
		        }
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function parameterList(): Context\ParameterListContext
		{
		    $localContext = new Context\ParameterListContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 12, self::RULE_parameterList);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(147);
		        $this->parameter();
		        $this->setState(152);
		        $this->errorHandler->sync($this);

		        $_la = $this->input->LA(1);
		        while ($_la === self::T__4) {
		        	$this->setState(148);
		        	$this->match(self::T__4);
		        	$this->setState(149);
		        	$this->parameter();
		        	$this->setState(154);
		        	$this->errorHandler->sync($this);
		        	$_la = $this->input->LA(1);
		        }
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function parameter(): Context\ParameterContext
		{
		    $localContext = new Context\ParameterContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 14, self::RULE_parameter);

		    try {
		        $this->setState(160);
		        $this->errorHandler->sync($this);

		        switch ($this->input->LA(1)) {
		            case self::ID:
		            	$localContext = new Context\NormalParameterContext($localContext);
		            	$this->enterOuterAlt($localContext, 1);
		            	$this->setState(155);
		            	$this->match(self::ID);
		            	$this->setState(156);
		            	$this->type();
		            	break;

		            case self::T__5:
		            	$localContext = new Context\PointerParameterContext($localContext);
		            	$this->enterOuterAlt($localContext, 2);
		            	$this->setState(157);
		            	$this->match(self::T__5);
		            	$this->setState(158);
		            	$this->match(self::ID);
		            	$this->setState(159);
		            	$this->type();
		            	break;

		        default:
		        	throw new NoViableAltException($this);
		        }
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function typeList(): Context\TypeListContext
		{
		    $localContext = new Context\TypeListContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 16, self::RULE_typeList);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(162);
		        $this->type();
		        $this->setState(167);
		        $this->errorHandler->sync($this);

		        $_la = $this->input->LA(1);
		        while ($_la === self::T__4) {
		        	$this->setState(163);
		        	$this->match(self::T__4);
		        	$this->setState(164);
		        	$this->type();
		        	$this->setState(169);
		        	$this->errorHandler->sync($this);
		        	$_la = $this->input->LA(1);
		        }
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function idList(): Context\IdListContext
		{
		    $localContext = new Context\IdListContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 18, self::RULE_idList);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(170);
		        $this->match(self::ID);
		        $this->setState(175);
		        $this->errorHandler->sync($this);

		        $_la = $this->input->LA(1);
		        while ($_la === self::T__4) {
		        	$this->setState(171);
		        	$this->match(self::T__4);
		        	$this->setState(172);
		        	$this->match(self::ID);
		        	$this->setState(177);
		        	$this->errorHandler->sync($this);
		        	$_la = $this->input->LA(1);
		        }
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function expressionList(): Context\ExpressionListContext
		{
		    $localContext = new Context\ExpressionListContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 20, self::RULE_expressionList);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(178);
		        $this->expression();
		        $this->setState(183);
		        $this->errorHandler->sync($this);

		        $alt = $this->getInterpreter()->adaptivePredict($this->input, 11, $this->ctx);

		        while ($alt !== 2 && $alt !== ATN::INVALID_ALT_NUMBER) {
		        	if ($alt === 1) {
		        		$this->setState(179);
		        		$this->match(self::T__4);
		        		$this->setState(180);
		        		$this->expression(); 
		        	}

		        	$this->setState(185);
		        	$this->errorHandler->sync($this);

		        	$alt = $this->getInterpreter()->adaptivePredict($this->input, 11, $this->ctx);
		        }
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function statement(): Context\StatementContext
		{
		    $localContext = new Context\StatementContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 22, self::RULE_statement);

		    try {
		        $this->setState(197);
		        $this->errorHandler->sync($this);

		        switch ($this->getInterpreter()->adaptivePredict($this->input, 12, $this->ctx)) {
		        	case 1:
		        	    $this->enterOuterAlt($localContext, 1);
		        	    $this->setState(186);
		        	    $this->shortVarDeclaration();
		        	break;

		        	case 2:
		        	    $this->enterOuterAlt($localContext, 2);
		        	    $this->setState(187);
		        	    $this->assignment();
		        	break;

		        	case 3:
		        	    $this->enterOuterAlt($localContext, 3);
		        	    $this->setState(188);
		        	    $this->ifStatement();
		        	break;

		        	case 4:
		        	    $this->enterOuterAlt($localContext, 4);
		        	    $this->setState(189);
		        	    $this->switchStatement();
		        	break;

		        	case 5:
		        	    $this->enterOuterAlt($localContext, 5);
		        	    $this->setState(190);
		        	    $this->forStatement();
		        	break;

		        	case 6:
		        	    $this->enterOuterAlt($localContext, 6);
		        	    $this->setState(191);
		        	    $this->breakStatement();
		        	break;

		        	case 7:
		        	    $this->enterOuterAlt($localContext, 7);
		        	    $this->setState(192);
		        	    $this->continueStatement();
		        	break;

		        	case 8:
		        	    $this->enterOuterAlt($localContext, 8);
		        	    $this->setState(193);
		        	    $this->returnStatement();
		        	break;

		        	case 9:
		        	    $this->enterOuterAlt($localContext, 9);
		        	    $this->setState(194);
		        	    $this->incDecStatement();
		        	break;

		        	case 10:
		        	    $this->enterOuterAlt($localContext, 10);
		        	    $this->setState(195);
		        	    $this->block();
		        	break;

		        	case 11:
		        	    $this->enterOuterAlt($localContext, 11);
		        	    $this->setState(196);
		        	    $this->expressionStatement();
		        	break;
		        }
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function assignment(): Context\AssignmentContext
		{
		    $localContext = new Context\AssignmentContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 24, self::RULE_assignment);

		    try {
		        $this->setState(220);
		        $this->errorHandler->sync($this);

		        switch ($this->getInterpreter()->adaptivePredict($this->input, 14, $this->ctx)) {
		        	case 1:
		        	    $localContext = new Context\SimpleAssignmentContext($localContext);
		        	    $this->enterOuterAlt($localContext, 1);
		        	    $this->setState(199);
		        	    $this->match(self::ID);
		        	    $this->setState(200);
		        	    $this->assignOp();
		        	    $this->setState(201);
		        	    $this->expression();
		        	break;

		        	case 2:
		        	    $localContext = new Context\ArrayAssignmentContext($localContext);
		        	    $this->enterOuterAlt($localContext, 2);
		        	    $this->setState(203);
		        	    $this->match(self::ID);
		        	    $this->setState(208); 
		        	    $this->errorHandler->sync($this);

		        	    $_la = $this->input->LA(1);
		        	    do {
		        	    	$this->setState(204);
		        	    	$this->match(self::T__6);
		        	    	$this->setState(205);
		        	    	$this->expression();
		        	    	$this->setState(206);
		        	    	$this->match(self::T__7);
		        	    	$this->setState(210); 
		        	    	$this->errorHandler->sync($this);
		        	    	$_la = $this->input->LA(1);
		        	    } while ($_la === self::T__6);
		        	    $this->setState(212);
		        	    $this->assignOp();
		        	    $this->setState(213);
		        	    $this->expression();
		        	break;

		        	case 3:
		        	    $localContext = new Context\PointerAssignmentContext($localContext);
		        	    $this->enterOuterAlt($localContext, 3);
		        	    $this->setState(215);
		        	    $this->match(self::T__5);
		        	    $this->setState(216);
		        	    $this->match(self::ID);
		        	    $this->setState(217);
		        	    $this->assignOp();
		        	    $this->setState(218);
		        	    $this->expression();
		        	break;
		        }
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function assignOp(): Context\AssignOpContext
		{
		    $localContext = new Context\AssignOpContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 26, self::RULE_assignOp);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(222);

		        $_la = $this->input->LA(1);

		        if (!(((($_la) & ~0x3f) === 0 && ((1 << $_la) & 7682) !== 0))) {
		        $this->errorHandler->recoverInline($this);
		        } else {
		        	if ($this->input->LA(1) === Token::EOF) {
		        	    $this->matchedEOF = true;
		            }

		        	$this->errorHandler->reportMatch($this);
		        	$this->consume();
		        }
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function incDecStatement(): Context\IncDecStatementContext
		{
		    $localContext = new Context\IncDecStatementContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 28, self::RULE_incDecStatement);

		    try {
		        $this->setState(228);
		        $this->errorHandler->sync($this);

		        switch ($this->getInterpreter()->adaptivePredict($this->input, 15, $this->ctx)) {
		        	case 1:
		        	    $localContext = new Context\IncrementStatementContext($localContext);
		        	    $this->enterOuterAlt($localContext, 1);
		        	    $this->setState(224);
		        	    $this->match(self::ID);
		        	    $this->setState(225);
		        	    $this->match(self::T__12);
		        	break;

		        	case 2:
		        	    $localContext = new Context\DecrementStatementContext($localContext);
		        	    $this->enterOuterAlt($localContext, 2);
		        	    $this->setState(226);
		        	    $this->match(self::ID);
		        	    $this->setState(227);
		        	    $this->match(self::T__13);
		        	break;
		        }
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function ifStatement(): Context\IfStatementContext
		{
		    $localContext = new Context\IfStatementContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 30, self::RULE_ifStatement);

		    try {
		        $localContext = new Context\IfElseIfElseContext($localContext);
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(230);
		        $this->match(self::IF);
		        $this->setState(231);
		        $this->expression();
		        $this->setState(232);
		        $this->block();
		        $this->setState(240);
		        $this->errorHandler->sync($this);

		        $alt = $this->getInterpreter()->adaptivePredict($this->input, 16, $this->ctx);

		        while ($alt !== 2 && $alt !== ATN::INVALID_ALT_NUMBER) {
		        	if ($alt === 1) {
		        		$this->setState(233);
		        		$this->match(self::ELSE);
		        		$this->setState(234);
		        		$this->match(self::IF);
		        		$this->setState(235);
		        		$this->expression();
		        		$this->setState(236);
		        		$this->block(); 
		        	}

		        	$this->setState(242);
		        	$this->errorHandler->sync($this);

		        	$alt = $this->getInterpreter()->adaptivePredict($this->input, 16, $this->ctx);
		        }
		        $this->setState(245);
		        $this->errorHandler->sync($this);
		        $_la = $this->input->LA(1);

		        if ($_la === self::ELSE) {
		        	$this->setState(243);
		        	$this->match(self::ELSE);
		        	$this->setState(244);
		        	$this->block();
		        }
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function switchStatement(): Context\SwitchStatementContext
		{
		    $localContext = new Context\SwitchStatementContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 32, self::RULE_switchStatement);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(247);
		        $this->match(self::SWITCH);
		        $this->setState(248);
		        $this->expression();
		        $this->setState(249);
		        $this->match(self::T__14);
		        $this->setState(253);
		        $this->errorHandler->sync($this);

		        $_la = $this->input->LA(1);
		        while ($_la === self::CASE) {
		        	$this->setState(250);
		        	$this->caseClause();
		        	$this->setState(255);
		        	$this->errorHandler->sync($this);
		        	$_la = $this->input->LA(1);
		        }
		        $this->setState(257);
		        $this->errorHandler->sync($this);
		        $_la = $this->input->LA(1);

		        if ($_la === self::DEFAULT) {
		        	$this->setState(256);
		        	$this->defaultClause();
		        }
		        $this->setState(259);
		        $this->match(self::T__15);
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function caseClause(): Context\CaseClauseContext
		{
		    $localContext = new Context\CaseClauseContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 34, self::RULE_caseClause);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(261);
		        $this->match(self::CASE);
		        $this->setState(262);
		        $this->expressionList();
		        $this->setState(263);
		        $this->match(self::T__16);
		        $this->setState(267);
		        $this->errorHandler->sync($this);

		        $_la = $this->input->LA(1);
		        while (((($_la) & ~0x3f) === 0 && ((1 << $_la) & 1117172330494197960) !== 0)) {
		        	$this->setState(264);
		        	$this->statement();
		        	$this->setState(269);
		        	$this->errorHandler->sync($this);
		        	$_la = $this->input->LA(1);
		        }
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function defaultClause(): Context\DefaultClauseContext
		{
		    $localContext = new Context\DefaultClauseContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 36, self::RULE_defaultClause);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(270);
		        $this->match(self::DEFAULT);
		        $this->setState(271);
		        $this->match(self::T__16);
		        $this->setState(275);
		        $this->errorHandler->sync($this);

		        $_la = $this->input->LA(1);
		        while (((($_la) & ~0x3f) === 0 && ((1 << $_la) & 1117172330494197960) !== 0)) {
		        	$this->setState(272);
		        	$this->statement();
		        	$this->setState(277);
		        	$this->errorHandler->sync($this);
		        	$_la = $this->input->LA(1);
		        }
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function forStatement(): Context\ForStatementContext
		{
		    $localContext = new Context\ForStatementContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 38, self::RULE_forStatement);

		    try {
		        $this->setState(288);
		        $this->errorHandler->sync($this);

		        switch ($this->getInterpreter()->adaptivePredict($this->input, 22, $this->ctx)) {
		        	case 1:
		        	    $localContext = new Context\ForTraditionalContext($localContext);
		        	    $this->enterOuterAlt($localContext, 1);
		        	    $this->setState(278);
		        	    $this->match(self::FOR);
		        	    $this->setState(279);
		        	    $this->forClause();
		        	    $this->setState(280);
		        	    $this->block();
		        	break;

		        	case 2:
		        	    $localContext = new Context\ForWhileContext($localContext);
		        	    $this->enterOuterAlt($localContext, 2);
		        	    $this->setState(282);
		        	    $this->match(self::FOR);
		        	    $this->setState(283);
		        	    $this->expression();
		        	    $this->setState(284);
		        	    $this->block();
		        	break;

		        	case 3:
		        	    $localContext = new Context\ForInfiniteContext($localContext);
		        	    $this->enterOuterAlt($localContext, 3);
		        	    $this->setState(286);
		        	    $this->match(self::FOR);
		        	    $this->setState(287);
		        	    $this->block();
		        	break;
		        }
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function forClause(): Context\ForClauseContext
		{
		    $localContext = new Context\ForClauseContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 40, self::RULE_forClause);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(290);
		        $this->forInit();
		        $this->setState(291);
		        $this->match(self::T__17);
		        $this->setState(293);
		        $this->errorHandler->sync($this);
		        $_la = $this->input->LA(1);

		        if (((($_la) & ~0x3f) === 0 && ((1 << $_la) & 1117139001547981000) !== 0)) {
		        	$this->setState(292);
		        	$this->expression();
		        }
		        $this->setState(295);
		        $this->match(self::T__17);
		        $this->setState(296);
		        $this->forPost();
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function forInit(): Context\ForInitContext
		{
		    $localContext = new Context\ForInitContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 42, self::RULE_forInit);

		    try {
		        $this->setState(303);
		        $this->errorHandler->sync($this);

		        switch ($this->getInterpreter()->adaptivePredict($this->input, 24, $this->ctx)) {
		        	case 1:
		        	    $this->enterOuterAlt($localContext, 1);
		        	    $this->setState(298);
		        	    $this->varDeclaration();
		        	break;

		        	case 2:
		        	    $this->enterOuterAlt($localContext, 2);
		        	    $this->setState(299);
		        	    $this->shortVarDeclaration();
		        	break;

		        	case 3:
		        	    $this->enterOuterAlt($localContext, 3);
		        	    $this->setState(300);
		        	    $this->assignment();
		        	break;

		        	case 4:
		        	    $this->enterOuterAlt($localContext, 4);
		        	    $this->setState(301);
		        	    $this->incDecStatement();
		        	break;

		        	case 5:
		        	    $this->enterOuterAlt($localContext, 5);

		        	break;
		        }
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function forPost(): Context\ForPostContext
		{
		    $localContext = new Context\ForPostContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 44, self::RULE_forPost);

		    try {
		        $this->setState(308);
		        $this->errorHandler->sync($this);

		        switch ($this->getInterpreter()->adaptivePredict($this->input, 25, $this->ctx)) {
		        	case 1:
		        	    $this->enterOuterAlt($localContext, 1);
		        	    $this->setState(305);
		        	    $this->assignment();
		        	break;

		        	case 2:
		        	    $this->enterOuterAlt($localContext, 2);
		        	    $this->setState(306);
		        	    $this->incDecStatement();
		        	break;

		        	case 3:
		        	    $this->enterOuterAlt($localContext, 3);

		        	break;
		        }
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function breakStatement(): Context\BreakStatementContext
		{
		    $localContext = new Context\BreakStatementContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 46, self::RULE_breakStatement);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(310);
		        $this->match(self::BREAK);
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function continueStatement(): Context\ContinueStatementContext
		{
		    $localContext = new Context\ContinueStatementContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 48, self::RULE_continueStatement);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(312);
		        $this->match(self::CONTINUE);
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function returnStatement(): Context\ReturnStatementContext
		{
		    $localContext = new Context\ReturnStatementContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 50, self::RULE_returnStatement);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(314);
		        $this->match(self::RETURN);
		        $this->setState(316);
		        $this->errorHandler->sync($this);

		        switch ($this->getInterpreter()->adaptivePredict($this->input, 26, $this->ctx)) {
		            case 1:
		        	    $this->setState(315);
		        	    $this->expressionList();
		        	break;
		        }
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function block(): Context\BlockContext
		{
		    $localContext = new Context\BlockContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 52, self::RULE_block);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(318);
		        $this->match(self::T__14);
		        $this->setState(322);
		        $this->errorHandler->sync($this);

		        $_la = $this->input->LA(1);
		        while (((($_la) & ~0x3f) === 0 && ((1 << $_la) & 1117172390623740104) !== 0)) {
		        	$this->setState(319);
		        	$this->declaration();
		        	$this->setState(324);
		        	$this->errorHandler->sync($this);
		        	$_la = $this->input->LA(1);
		        }
		        $this->setState(325);
		        $this->match(self::T__15);
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function expressionStatement(): Context\ExpressionStatementContext
		{
		    $localContext = new Context\ExpressionStatementContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 54, self::RULE_expressionStatement);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(327);
		        $this->expression();
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function expression(): Context\ExpressionContext
		{
		    $localContext = new Context\ExpressionContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 56, self::RULE_expression);

		    try {
		        $this->setState(336);
		        $this->errorHandler->sync($this);

		        switch ($this->getInterpreter()->adaptivePredict($this->input, 28, $this->ctx)) {
		        	case 1:
		        	    $localContext = new Context\TernaryExprContext($localContext);
		        	    $this->enterOuterAlt($localContext, 1);
		        	    $this->setState(329);
		        	    $this->logicalOr();
		        	    $this->setState(330);
		        	    $this->match(self::T__18);
		        	    $this->setState(331);
		        	    $this->expression();
		        	    $this->setState(332);
		        	    $this->match(self::T__16);
		        	    $this->setState(333);
		        	    $this->expression();
		        	break;

		        	case 2:
		        	    $localContext = new Context\NonTernaryExprContext($localContext);
		        	    $this->enterOuterAlt($localContext, 2);
		        	    $this->setState(335);
		        	    $this->logicalOr();
		        	break;
		        }
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function logicalOr(): Context\LogicalOrContext
		{
		    $localContext = new Context\LogicalOrContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 58, self::RULE_logicalOr);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(338);
		        $this->logicalAnd();
		        $this->setState(343);
		        $this->errorHandler->sync($this);

		        $_la = $this->input->LA(1);
		        while ($_la === self::OR) {
		        	$this->setState(339);
		        	$this->match(self::OR);
		        	$this->setState(340);
		        	$this->logicalAnd();
		        	$this->setState(345);
		        	$this->errorHandler->sync($this);
		        	$_la = $this->input->LA(1);
		        }
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function logicalAnd(): Context\LogicalAndContext
		{
		    $localContext = new Context\LogicalAndContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 60, self::RULE_logicalAnd);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(346);
		        $this->equality();
		        $this->setState(351);
		        $this->errorHandler->sync($this);

		        $_la = $this->input->LA(1);
		        while ($_la === self::AND) {
		        	$this->setState(347);
		        	$this->match(self::AND);
		        	$this->setState(348);
		        	$this->equality();
		        	$this->setState(353);
		        	$this->errorHandler->sync($this);
		        	$_la = $this->input->LA(1);
		        }
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function equality(): Context\EqualityContext
		{
		    $localContext = new Context\EqualityContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 62, self::RULE_equality);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(354);
		        $this->relational();
		        $this->setState(359);
		        $this->errorHandler->sync($this);

		        $_la = $this->input->LA(1);
		        while ($_la === self::T__19 || $_la === self::T__20) {
		        	$this->setState(355);

		        	$_la = $this->input->LA(1);

		        	if (!($_la === self::T__19 || $_la === self::T__20)) {
		        	$this->errorHandler->recoverInline($this);
		        	} else {
		        		if ($this->input->LA(1) === Token::EOF) {
		        		    $this->matchedEOF = true;
		        	    }

		        		$this->errorHandler->reportMatch($this);
		        		$this->consume();
		        	}
		        	$this->setState(356);
		        	$this->relational();
		        	$this->setState(361);
		        	$this->errorHandler->sync($this);
		        	$_la = $this->input->LA(1);
		        }
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function relational(): Context\RelationalContext
		{
		    $localContext = new Context\RelationalContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 64, self::RULE_relational);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(362);
		        $this->additive();
		        $this->setState(367);
		        $this->errorHandler->sync($this);

		        $_la = $this->input->LA(1);
		        while (((($_la) & ~0x3f) === 0 && ((1 << $_la) & 62914560) !== 0)) {
		        	$this->setState(363);

		        	$_la = $this->input->LA(1);

		        	if (!(((($_la) & ~0x3f) === 0 && ((1 << $_la) & 62914560) !== 0))) {
		        	$this->errorHandler->recoverInline($this);
		        	} else {
		        		if ($this->input->LA(1) === Token::EOF) {
		        		    $this->matchedEOF = true;
		        	    }

		        		$this->errorHandler->reportMatch($this);
		        		$this->consume();
		        	}
		        	$this->setState(364);
		        	$this->additive();
		        	$this->setState(369);
		        	$this->errorHandler->sync($this);
		        	$_la = $this->input->LA(1);
		        }
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function additive(): Context\AdditiveContext
		{
		    $localContext = new Context\AdditiveContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 66, self::RULE_additive);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(370);
		        $this->multiplicative();
		        $this->setState(375);
		        $this->errorHandler->sync($this);

		        $alt = $this->getInterpreter()->adaptivePredict($this->input, 33, $this->ctx);

		        while ($alt !== 2 && $alt !== ATN::INVALID_ALT_NUMBER) {
		        	if ($alt === 1) {
		        		$this->setState(371);

		        		$_la = $this->input->LA(1);

		        		if (!($_la === self::T__25 || $_la === self::T__26)) {
		        		$this->errorHandler->recoverInline($this);
		        		} else {
		        			if ($this->input->LA(1) === Token::EOF) {
		        			    $this->matchedEOF = true;
		        		    }

		        			$this->errorHandler->reportMatch($this);
		        			$this->consume();
		        		}
		        		$this->setState(372);
		        		$this->multiplicative(); 
		        	}

		        	$this->setState(377);
		        	$this->errorHandler->sync($this);

		        	$alt = $this->getInterpreter()->adaptivePredict($this->input, 33, $this->ctx);
		        }
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function multiplicative(): Context\MultiplicativeContext
		{
		    $localContext = new Context\MultiplicativeContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 68, self::RULE_multiplicative);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(378);
		        $this->unary();
		        $this->setState(383);
		        $this->errorHandler->sync($this);

		        $alt = $this->getInterpreter()->adaptivePredict($this->input, 34, $this->ctx);

		        while ($alt !== 2 && $alt !== ATN::INVALID_ALT_NUMBER) {
		        	if ($alt === 1) {
		        		$this->setState(379);

		        		$_la = $this->input->LA(1);

		        		if (!(((($_la) & ~0x3f) === 0 && ((1 << $_la) & 805306432) !== 0))) {
		        		$this->errorHandler->recoverInline($this);
		        		} else {
		        			if ($this->input->LA(1) === Token::EOF) {
		        			    $this->matchedEOF = true;
		        		    }

		        			$this->errorHandler->reportMatch($this);
		        			$this->consume();
		        		}
		        		$this->setState(380);
		        		$this->unary(); 
		        	}

		        	$this->setState(385);
		        	$this->errorHandler->sync($this);

		        	$alt = $this->getInterpreter()->adaptivePredict($this->input, 34, $this->ctx);
		        }
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function unary(): Context\UnaryContext
		{
		    $localContext = new Context\UnaryContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 70, self::RULE_unary);

		    try {
		        $this->setState(395);
		        $this->errorHandler->sync($this);

		        switch ($this->input->LA(1)) {
		            case self::T__2:
		            case self::T__6:
		            case self::T__14:
		            case self::TRUE:
		            case self::FALSE:
		            case self::NIL:
		            case self::FLOAT32:
		            case self::INT32:
		            case self::RUNE:
		            case self::STRING:
		            case self::ID:
		            	$localContext = new Context\PrimaryUnaryContext($localContext);
		            	$this->enterOuterAlt($localContext, 1);
		            	$this->setState(386);
		            	$this->primary();
		            	break;

		            case self::T__26:
		            	$localContext = new Context\NegativeUnaryContext($localContext);
		            	$this->enterOuterAlt($localContext, 2);
		            	$this->setState(387);
		            	$this->match(self::T__26);
		            	$this->setState(388);
		            	$this->unary();
		            	break;

		            case self::T__29:
		            	$localContext = new Context\NotUnaryContext($localContext);
		            	$this->enterOuterAlt($localContext, 3);
		            	$this->setState(389);
		            	$this->match(self::T__29);
		            	$this->setState(390);
		            	$this->unary();
		            	break;

		            case self::T__30:
		            	$localContext = new Context\AddressOfContext($localContext);
		            	$this->enterOuterAlt($localContext, 4);
		            	$this->setState(391);
		            	$this->match(self::T__30);
		            	$this->setState(392);
		            	$this->match(self::ID);
		            	break;

		            case self::T__5:
		            	$localContext = new Context\DereferenceContext($localContext);
		            	$this->enterOuterAlt($localContext, 5);
		            	$this->setState(393);
		            	$this->match(self::T__5);
		            	$this->setState(394);
		            	$this->unary();
		            	break;

		        default:
		        	throw new NoViableAltException($this);
		        }
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function primary(): Context\PrimaryContext
		{
		    $localContext = new Context\PrimaryContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 72, self::RULE_primary);

		    try {
		        $this->setState(433);
		        $this->errorHandler->sync($this);

		        switch ($this->getInterpreter()->adaptivePredict($this->input, 39, $this->ctx)) {
		        	case 1:
		        	    $localContext = new Context\IntLiteralContext($localContext);
		        	    $this->enterOuterAlt($localContext, 1);
		        	    $this->setState(397);
		        	    $this->match(self::INT32);
		        	break;

		        	case 2:
		        	    $localContext = new Context\FloatLiteralContext($localContext);
		        	    $this->enterOuterAlt($localContext, 2);
		        	    $this->setState(398);
		        	    $this->match(self::FLOAT32);
		        	break;

		        	case 3:
		        	    $localContext = new Context\RuneLiteralContext($localContext);
		        	    $this->enterOuterAlt($localContext, 3);
		        	    $this->setState(399);
		        	    $this->match(self::RUNE);
		        	break;

		        	case 4:
		        	    $localContext = new Context\StringLiteralContext($localContext);
		        	    $this->enterOuterAlt($localContext, 4);
		        	    $this->setState(400);
		        	    $this->match(self::STRING);
		        	break;

		        	case 5:
		        	    $localContext = new Context\TrueLiteralContext($localContext);
		        	    $this->enterOuterAlt($localContext, 5);
		        	    $this->setState(401);
		        	    $this->match(self::TRUE);
		        	break;

		        	case 6:
		        	    $localContext = new Context\FalseLiteralContext($localContext);
		        	    $this->enterOuterAlt($localContext, 6);
		        	    $this->setState(402);
		        	    $this->match(self::FALSE);
		        	break;

		        	case 7:
		        	    $localContext = new Context\NilLiteralContext($localContext);
		        	    $this->enterOuterAlt($localContext, 7);
		        	    $this->setState(403);
		        	    $this->match(self::NIL);
		        	break;

		        	case 8:
		        	    $localContext = new Context\FunctionCallContext($localContext);
		        	    $this->enterOuterAlt($localContext, 8);
		        	    $this->setState(404);
		        	    $this->match(self::ID);
		        	    $this->setState(407);
		        	    $this->errorHandler->sync($this);
		        	    $_la = $this->input->LA(1);

		        	    if ($_la === self::T__31) {
		        	    	$this->setState(405);
		        	    	$this->match(self::T__31);
		        	    	$this->setState(406);
		        	    	$this->match(self::ID);
		        	    }
		        	    $this->setState(409);
		        	    $this->match(self::T__2);
		        	    $this->setState(411);
		        	    $this->errorHandler->sync($this);
		        	    $_la = $this->input->LA(1);

		        	    if (((($_la) & ~0x3f) === 0 && ((1 << $_la) & 1117139001547981000) !== 0)) {
		        	    	$this->setState(410);
		        	    	$this->argumentList();
		        	    }
		        	    $this->setState(413);
		        	    $this->match(self::T__3);
		        	break;

		        	case 9:
		        	    $localContext = new Context\ArrayAccessContext($localContext);
		        	    $this->enterOuterAlt($localContext, 9);
		        	    $this->setState(414);
		        	    $this->match(self::ID);
		        	    $this->setState(419); 
		        	    $this->errorHandler->sync($this);

		        	    $alt = 1;

		        	    do {
		        	    	switch ($alt) {
		        	    	case 1:
		        	    		$this->setState(415);
		        	    		$this->match(self::T__6);
		        	    		$this->setState(416);
		        	    		$this->expression();
		        	    		$this->setState(417);
		        	    		$this->match(self::T__7);
		        	    		break;
		        	    	default:
		        	    		throw new NoViableAltException($this);
		        	    	}

		        	    	$this->setState(421); 
		        	    	$this->errorHandler->sync($this);

		        	    	$alt = $this->getInterpreter()->adaptivePredict($this->input, 38, $this->ctx);
		        	    } while ($alt !== 2 && $alt !== ATN::INVALID_ALT_NUMBER);
		        	break;

		        	case 10:
		        	    $localContext = new Context\IdentifierContext($localContext);
		        	    $this->enterOuterAlt($localContext, 10);
		        	    $this->setState(423);
		        	    $this->match(self::ID);
		        	break;

		        	case 11:
		        	    $localContext = new Context\GroupedExpressionContext($localContext);
		        	    $this->enterOuterAlt($localContext, 11);
		        	    $this->setState(424);
		        	    $this->match(self::T__2);
		        	    $this->setState(425);
		        	    $this->expression();
		        	    $this->setState(426);
		        	    $this->match(self::T__3);
		        	break;

		        	case 12:
		        	    $localContext = new Context\ArrayLiteralExprContext($localContext);
		        	    $this->enterOuterAlt($localContext, 12);
		        	    $this->setState(428);
		        	    $this->arrayLiteral();
		        	break;

		        	case 13:
		        	    $localContext = new Context\InnerArrayLiteralContext($localContext);
		        	    $this->enterOuterAlt($localContext, 13);
		        	    $this->setState(429);
		        	    $this->match(self::T__14);
		        	    $this->setState(430);
		        	    $this->expressionList();
		        	    $this->setState(431);
		        	    $this->match(self::T__15);
		        	break;
		        }
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function arrayLiteral(): Context\ArrayLiteralContext
		{
		    $localContext = new Context\ArrayLiteralContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 74, self::RULE_arrayLiteral);

		    try {
		        $this->setState(455);
		        $this->errorHandler->sync($this);

		        switch ($this->getInterpreter()->adaptivePredict($this->input, 42, $this->ctx)) {
		        	case 1:
		        	    $localContext = new Context\FixedArrayLiteralNodeContext($localContext);
		        	    $this->enterOuterAlt($localContext, 1);
		        	    $this->setState(435);
		        	    $this->match(self::T__6);
		        	    $this->setState(436);
		        	    $this->expression();
		        	    $this->setState(437);
		        	    $this->match(self::T__7);
		        	    $this->setState(438);
		        	    $this->type();
		        	    $this->setState(439);
		        	    $this->match(self::T__14);
		        	    $this->setState(442);
		        	    $this->errorHandler->sync($this);

		        	    switch ($this->getInterpreter()->adaptivePredict($this->input, 40, $this->ctx)) {
		        	        case 1:
		        	    	    $this->setState(440);
		        	    	    $this->expressionList();
		        	    	break;

		        	        case 2:
		        	    	    $this->setState(441);
		        	    	    $this->innerLiteralList();
		        	    	break;
		        	    }
		        	    $this->setState(444);
		        	    $this->match(self::T__15);
		        	break;

		        	case 2:
		        	    $localContext = new Context\SliceLiteralNodeContext($localContext);
		        	    $this->enterOuterAlt($localContext, 2);
		        	    $this->setState(446);
		        	    $this->match(self::T__6);
		        	    $this->setState(447);
		        	    $this->match(self::T__7);
		        	    $this->setState(448);
		        	    $this->type();
		        	    $this->setState(449);
		        	    $this->match(self::T__14);
		        	    $this->setState(451);
		        	    $this->errorHandler->sync($this);
		        	    $_la = $this->input->LA(1);

		        	    if (((($_la) & ~0x3f) === 0 && ((1 << $_la) & 1117139001547981000) !== 0)) {
		        	    	$this->setState(450);
		        	    	$this->expressionList();
		        	    }
		        	    $this->setState(453);
		        	    $this->match(self::T__15);
		        	break;
		        }
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function innerLiteralList(): Context\InnerLiteralListContext
		{
		    $localContext = new Context\InnerLiteralListContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 76, self::RULE_innerLiteralList);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(457);
		        $this->innerLiteral();
		        $this->setState(462);
		        $this->errorHandler->sync($this);

		        $alt = $this->getInterpreter()->adaptivePredict($this->input, 43, $this->ctx);

		        while ($alt !== 2 && $alt !== ATN::INVALID_ALT_NUMBER) {
		        	if ($alt === 1) {
		        		$this->setState(458);
		        		$this->match(self::T__4);
		        		$this->setState(459);
		        		$this->innerLiteral(); 
		        	}

		        	$this->setState(464);
		        	$this->errorHandler->sync($this);

		        	$alt = $this->getInterpreter()->adaptivePredict($this->input, 43, $this->ctx);
		        }
		        $this->setState(466);
		        $this->errorHandler->sync($this);
		        $_la = $this->input->LA(1);

		        if ($_la === self::T__4) {
		        	$this->setState(465);
		        	$this->match(self::T__4);
		        }
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function innerLiteral(): Context\InnerLiteralContext
		{
		    $localContext = new Context\InnerLiteralContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 78, self::RULE_innerLiteral);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(468);
		        $this->match(self::T__14);
		        $this->setState(469);
		        $this->expressionList();
		        $this->setState(471);
		        $this->errorHandler->sync($this);
		        $_la = $this->input->LA(1);

		        if ($_la === self::T__4) {
		        	$this->setState(470);
		        	$this->match(self::T__4);
		        }
		        $this->setState(473);
		        $this->match(self::T__15);
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function argumentList(): Context\ArgumentListContext
		{
		    $localContext = new Context\ArgumentListContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 80, self::RULE_argumentList);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(475);
		        $this->argument();
		        $this->setState(480);
		        $this->errorHandler->sync($this);

		        $_la = $this->input->LA(1);
		        while ($_la === self::T__4) {
		        	$this->setState(476);
		        	$this->match(self::T__4);
		        	$this->setState(477);
		        	$this->argument();
		        	$this->setState(482);
		        	$this->errorHandler->sync($this);
		        	$_la = $this->input->LA(1);
		        }
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function argument(): Context\ArgumentContext
		{
		    $localContext = new Context\ArgumentContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 82, self::RULE_argument);

		    try {
		        $this->setState(486);
		        $this->errorHandler->sync($this);

		        switch ($this->getInterpreter()->adaptivePredict($this->input, 47, $this->ctx)) {
		        	case 1:
		        	    $localContext = new Context\ExpressionArgumentContext($localContext);
		        	    $this->enterOuterAlt($localContext, 1);
		        	    $this->setState(483);
		        	    $this->expression();
		        	break;

		        	case 2:
		        	    $localContext = new Context\AddressArgumentContext($localContext);
		        	    $this->enterOuterAlt($localContext, 2);
		        	    $this->setState(484);
		        	    $this->match(self::T__30);
		        	    $this->setState(485);
		        	    $this->match(self::ID);
		        	break;
		        }
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function type(): Context\TypeContext
		{
		    $localContext = new Context\TypeContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 84, self::RULE_type);

		    try {
		        $this->setState(503);
		        $this->errorHandler->sync($this);

		        switch ($this->getInterpreter()->adaptivePredict($this->input, 48, $this->ctx)) {
		        	case 1:
		        	    $localContext = new Context\Int32TypeContext($localContext);
		        	    $this->enterOuterAlt($localContext, 1);
		        	    $this->setState(488);
		        	    $this->match(self::INT32_TYPE);
		        	break;

		        	case 2:
		        	    $localContext = new Context\Float32TypeContext($localContext);
		        	    $this->enterOuterAlt($localContext, 2);
		        	    $this->setState(489);
		        	    $this->match(self::FLOAT32_TYPE);
		        	break;

		        	case 3:
		        	    $localContext = new Context\BoolTypeContext($localContext);
		        	    $this->enterOuterAlt($localContext, 3);
		        	    $this->setState(490);
		        	    $this->match(self::BOOL_TYPE);
		        	break;

		        	case 4:
		        	    $localContext = new Context\RuneTypeContext($localContext);
		        	    $this->enterOuterAlt($localContext, 4);
		        	    $this->setState(491);
		        	    $this->match(self::RUNE_TYPE);
		        	break;

		        	case 5:
		        	    $localContext = new Context\StringTypeContext($localContext);
		        	    $this->enterOuterAlt($localContext, 5);
		        	    $this->setState(492);
		        	    $this->match(self::STRING_TYPE);
		        	break;

		        	case 6:
		        	    $localContext = new Context\ArrayTypeContext($localContext);
		        	    $this->enterOuterAlt($localContext, 6);
		        	    $this->setState(493);
		        	    $this->match(self::T__6);
		        	    $this->setState(494);
		        	    $this->expression();
		        	    $this->setState(495);
		        	    $this->match(self::T__7);
		        	    $this->setState(496);
		        	    $this->type();
		        	break;

		        	case 7:
		        	    $localContext = new Context\SliceTypeContext($localContext);
		        	    $this->enterOuterAlt($localContext, 7);
		        	    $this->setState(498);
		        	    $this->match(self::T__6);
		        	    $this->setState(499);
		        	    $this->match(self::T__7);
		        	    $this->setState(500);
		        	    $this->type();
		        	break;

		        	case 8:
		        	    $localContext = new Context\PointerTypeContext($localContext);
		        	    $this->enterOuterAlt($localContext, 8);
		        	    $this->setState(501);
		        	    $this->match(self::T__5);
		        	    $this->setState(502);
		        	    $this->type();
		        	break;
		        }
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}
	}
}

namespace Context {
	use Antlr\Antlr4\Runtime\ParserRuleContext;
	use Antlr\Antlr4\Runtime\Token;
	use Antlr\Antlr4\Runtime\Tree\ParseTreeVisitor;
	use Antlr\Antlr4\Runtime\Tree\TerminalNode;
	use Antlr\Antlr4\Runtime\Tree\ParseTreeListener;
	use GolampiParser;
	use GolampiVisitor;

	class ProgramContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GolampiParser::RULE_program;
	    }

	    public function EOF(): ?TerminalNode
	    {
	        return $this->getToken(GolampiParser::EOF, 0);
	    }

	    /**
	     * @return array<DeclarationContext>|DeclarationContext|null
	     */
	    public function declaration(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTypedRuleContexts(DeclarationContext::class);
	    	}

	        return $this->getTypedRuleContext(DeclarationContext::class, $index);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitProgram($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class DeclarationContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GolampiParser::RULE_declaration;
	    }

	    public function varDeclaration(): ?VarDeclarationContext
	    {
	    	return $this->getTypedRuleContext(VarDeclarationContext::class, 0);
	    }

	    public function constDeclaration(): ?ConstDeclarationContext
	    {
	    	return $this->getTypedRuleContext(ConstDeclarationContext::class, 0);
	    }

	    public function functionDeclaration(): ?FunctionDeclarationContext
	    {
	    	return $this->getTypedRuleContext(FunctionDeclarationContext::class, 0);
	    }

	    public function statement(): ?StatementContext
	    {
	    	return $this->getTypedRuleContext(StatementContext::class, 0);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitDeclaration($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class VarDeclarationContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GolampiParser::RULE_varDeclaration;
	    }
	 
		public function copyFrom(ParserRuleContext $context): void
		{
			parent::copyFrom($context);

		}
	}

	class VarDeclSimpleContext extends VarDeclarationContext
	{
		public function __construct(VarDeclarationContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    public function VAR(): ?TerminalNode
	    {
	        return $this->getToken(GolampiParser::VAR, 0);
	    }

	    public function idList(): ?IdListContext
	    {
	    	return $this->getTypedRuleContext(IdListContext::class, 0);
	    }

	    public function type(): ?TypeContext
	    {
	    	return $this->getTypedRuleContext(TypeContext::class, 0);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitVarDeclSimple($this);
		    }

			return $visitor->visitChildren($this);
		}
	}

	class VarDeclWithInitContext extends VarDeclarationContext
	{
		public function __construct(VarDeclarationContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    public function VAR(): ?TerminalNode
	    {
	        return $this->getToken(GolampiParser::VAR, 0);
	    }

	    public function idList(): ?IdListContext
	    {
	    	return $this->getTypedRuleContext(IdListContext::class, 0);
	    }

	    public function type(): ?TypeContext
	    {
	    	return $this->getTypedRuleContext(TypeContext::class, 0);
	    }

	    public function expressionList(): ?ExpressionListContext
	    {
	    	return $this->getTypedRuleContext(ExpressionListContext::class, 0);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitVarDeclWithInit($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class ShortVarDeclarationContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GolampiParser::RULE_shortVarDeclaration;
	    }
	 
		public function copyFrom(ParserRuleContext $context): void
		{
			parent::copyFrom($context);

		}
	}

	class ShortVarDeclContext extends ShortVarDeclarationContext
	{
		public function __construct(ShortVarDeclarationContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    public function idList(): ?IdListContext
	    {
	    	return $this->getTypedRuleContext(IdListContext::class, 0);
	    }

	    public function expressionList(): ?ExpressionListContext
	    {
	    	return $this->getTypedRuleContext(ExpressionListContext::class, 0);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitShortVarDecl($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class ConstDeclarationContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GolampiParser::RULE_constDeclaration;
	    }
	 
		public function copyFrom(ParserRuleContext $context): void
		{
			parent::copyFrom($context);

		}
	}

	class ConstDeclContext extends ConstDeclarationContext
	{
		public function __construct(ConstDeclarationContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    public function CONST(): ?TerminalNode
	    {
	        return $this->getToken(GolampiParser::CONST, 0);
	    }

	    public function ID(): ?TerminalNode
	    {
	        return $this->getToken(GolampiParser::ID, 0);
	    }

	    public function type(): ?TypeContext
	    {
	    	return $this->getTypedRuleContext(TypeContext::class, 0);
	    }

	    public function expression(): ?ExpressionContext
	    {
	    	return $this->getTypedRuleContext(ExpressionContext::class, 0);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitConstDecl($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class FunctionDeclarationContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GolampiParser::RULE_functionDeclaration;
	    }
	 
		public function copyFrom(ParserRuleContext $context): void
		{
			parent::copyFrom($context);

		}
	}

	class FuncDeclSingleReturnContext extends FunctionDeclarationContext
	{
		public function __construct(FunctionDeclarationContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    public function FUNC(): ?TerminalNode
	    {
	        return $this->getToken(GolampiParser::FUNC, 0);
	    }

	    public function ID(): ?TerminalNode
	    {
	        return $this->getToken(GolampiParser::ID, 0);
	    }

	    public function block(): ?BlockContext
	    {
	    	return $this->getTypedRuleContext(BlockContext::class, 0);
	    }

	    public function parameterList(): ?ParameterListContext
	    {
	    	return $this->getTypedRuleContext(ParameterListContext::class, 0);
	    }

	    public function type(): ?TypeContext
	    {
	    	return $this->getTypedRuleContext(TypeContext::class, 0);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitFuncDeclSingleReturn($this);
		    }

			return $visitor->visitChildren($this);
		}
	}

	class FuncDeclMultiReturnContext extends FunctionDeclarationContext
	{
		public function __construct(FunctionDeclarationContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    public function FUNC(): ?TerminalNode
	    {
	        return $this->getToken(GolampiParser::FUNC, 0);
	    }

	    public function ID(): ?TerminalNode
	    {
	        return $this->getToken(GolampiParser::ID, 0);
	    }

	    public function typeList(): ?TypeListContext
	    {
	    	return $this->getTypedRuleContext(TypeListContext::class, 0);
	    }

	    public function block(): ?BlockContext
	    {
	    	return $this->getTypedRuleContext(BlockContext::class, 0);
	    }

	    public function parameterList(): ?ParameterListContext
	    {
	    	return $this->getTypedRuleContext(ParameterListContext::class, 0);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitFuncDeclMultiReturn($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class ParameterListContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GolampiParser::RULE_parameterList;
	    }

	    /**
	     * @return array<ParameterContext>|ParameterContext|null
	     */
	    public function parameter(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTypedRuleContexts(ParameterContext::class);
	    	}

	        return $this->getTypedRuleContext(ParameterContext::class, $index);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitParameterList($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class ParameterContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GolampiParser::RULE_parameter;
	    }
	 
		public function copyFrom(ParserRuleContext $context): void
		{
			parent::copyFrom($context);

		}
	}

	class NormalParameterContext extends ParameterContext
	{
		public function __construct(ParameterContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    public function ID(): ?TerminalNode
	    {
	        return $this->getToken(GolampiParser::ID, 0);
	    }

	    public function type(): ?TypeContext
	    {
	    	return $this->getTypedRuleContext(TypeContext::class, 0);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitNormalParameter($this);
		    }

			return $visitor->visitChildren($this);
		}
	}

	class PointerParameterContext extends ParameterContext
	{
		public function __construct(ParameterContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    public function ID(): ?TerminalNode
	    {
	        return $this->getToken(GolampiParser::ID, 0);
	    }

	    public function type(): ?TypeContext
	    {
	    	return $this->getTypedRuleContext(TypeContext::class, 0);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitPointerParameter($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class TypeListContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GolampiParser::RULE_typeList;
	    }

	    /**
	     * @return array<TypeContext>|TypeContext|null
	     */
	    public function type(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTypedRuleContexts(TypeContext::class);
	    	}

	        return $this->getTypedRuleContext(TypeContext::class, $index);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitTypeList($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class IdListContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GolampiParser::RULE_idList;
	    }

	    /**
	     * @return array<TerminalNode>|TerminalNode|null
	     */
	    public function ID(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTokens(GolampiParser::ID);
	    	}

	        return $this->getToken(GolampiParser::ID, $index);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitIdList($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class ExpressionListContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GolampiParser::RULE_expressionList;
	    }

	    /**
	     * @return array<ExpressionContext>|ExpressionContext|null
	     */
	    public function expression(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTypedRuleContexts(ExpressionContext::class);
	    	}

	        return $this->getTypedRuleContext(ExpressionContext::class, $index);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitExpressionList($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class StatementContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GolampiParser::RULE_statement;
	    }

	    public function shortVarDeclaration(): ?ShortVarDeclarationContext
	    {
	    	return $this->getTypedRuleContext(ShortVarDeclarationContext::class, 0);
	    }

	    public function assignment(): ?AssignmentContext
	    {
	    	return $this->getTypedRuleContext(AssignmentContext::class, 0);
	    }

	    public function ifStatement(): ?IfStatementContext
	    {
	    	return $this->getTypedRuleContext(IfStatementContext::class, 0);
	    }

	    public function switchStatement(): ?SwitchStatementContext
	    {
	    	return $this->getTypedRuleContext(SwitchStatementContext::class, 0);
	    }

	    public function forStatement(): ?ForStatementContext
	    {
	    	return $this->getTypedRuleContext(ForStatementContext::class, 0);
	    }

	    public function breakStatement(): ?BreakStatementContext
	    {
	    	return $this->getTypedRuleContext(BreakStatementContext::class, 0);
	    }

	    public function continueStatement(): ?ContinueStatementContext
	    {
	    	return $this->getTypedRuleContext(ContinueStatementContext::class, 0);
	    }

	    public function returnStatement(): ?ReturnStatementContext
	    {
	    	return $this->getTypedRuleContext(ReturnStatementContext::class, 0);
	    }

	    public function incDecStatement(): ?IncDecStatementContext
	    {
	    	return $this->getTypedRuleContext(IncDecStatementContext::class, 0);
	    }

	    public function block(): ?BlockContext
	    {
	    	return $this->getTypedRuleContext(BlockContext::class, 0);
	    }

	    public function expressionStatement(): ?ExpressionStatementContext
	    {
	    	return $this->getTypedRuleContext(ExpressionStatementContext::class, 0);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitStatement($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class AssignmentContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GolampiParser::RULE_assignment;
	    }
	 
		public function copyFrom(ParserRuleContext $context): void
		{
			parent::copyFrom($context);

		}
	}

	class PointerAssignmentContext extends AssignmentContext
	{
		public function __construct(AssignmentContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    public function ID(): ?TerminalNode
	    {
	        return $this->getToken(GolampiParser::ID, 0);
	    }

	    public function assignOp(): ?AssignOpContext
	    {
	    	return $this->getTypedRuleContext(AssignOpContext::class, 0);
	    }

	    public function expression(): ?ExpressionContext
	    {
	    	return $this->getTypedRuleContext(ExpressionContext::class, 0);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitPointerAssignment($this);
		    }

			return $visitor->visitChildren($this);
		}
	}

	class SimpleAssignmentContext extends AssignmentContext
	{
		public function __construct(AssignmentContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    public function ID(): ?TerminalNode
	    {
	        return $this->getToken(GolampiParser::ID, 0);
	    }

	    public function assignOp(): ?AssignOpContext
	    {
	    	return $this->getTypedRuleContext(AssignOpContext::class, 0);
	    }

	    public function expression(): ?ExpressionContext
	    {
	    	return $this->getTypedRuleContext(ExpressionContext::class, 0);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitSimpleAssignment($this);
		    }

			return $visitor->visitChildren($this);
		}
	}

	class ArrayAssignmentContext extends AssignmentContext
	{
		public function __construct(AssignmentContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    public function ID(): ?TerminalNode
	    {
	        return $this->getToken(GolampiParser::ID, 0);
	    }

	    public function assignOp(): ?AssignOpContext
	    {
	    	return $this->getTypedRuleContext(AssignOpContext::class, 0);
	    }

	    /**
	     * @return array<ExpressionContext>|ExpressionContext|null
	     */
	    public function expression(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTypedRuleContexts(ExpressionContext::class);
	    	}

	        return $this->getTypedRuleContext(ExpressionContext::class, $index);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitArrayAssignment($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class AssignOpContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GolampiParser::RULE_assignOp;
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitAssignOp($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class IncDecStatementContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GolampiParser::RULE_incDecStatement;
	    }
	 
		public function copyFrom(ParserRuleContext $context): void
		{
			parent::copyFrom($context);

		}
	}

	class DecrementStatementContext extends IncDecStatementContext
	{
		public function __construct(IncDecStatementContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    public function ID(): ?TerminalNode
	    {
	        return $this->getToken(GolampiParser::ID, 0);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitDecrementStatement($this);
		    }

			return $visitor->visitChildren($this);
		}
	}

	class IncrementStatementContext extends IncDecStatementContext
	{
		public function __construct(IncDecStatementContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    public function ID(): ?TerminalNode
	    {
	        return $this->getToken(GolampiParser::ID, 0);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitIncrementStatement($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class IfStatementContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GolampiParser::RULE_ifStatement;
	    }
	 
		public function copyFrom(ParserRuleContext $context): void
		{
			parent::copyFrom($context);

		}
	}

	class IfElseIfElseContext extends IfStatementContext
	{
		public function __construct(IfStatementContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    /**
	     * @return array<TerminalNode>|TerminalNode|null
	     */
	    public function IF(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTokens(GolampiParser::IF);
	    	}

	        return $this->getToken(GolampiParser::IF, $index);
	    }

	    /**
	     * @return array<ExpressionContext>|ExpressionContext|null
	     */
	    public function expression(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTypedRuleContexts(ExpressionContext::class);
	    	}

	        return $this->getTypedRuleContext(ExpressionContext::class, $index);
	    }

	    /**
	     * @return array<BlockContext>|BlockContext|null
	     */
	    public function block(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTypedRuleContexts(BlockContext::class);
	    	}

	        return $this->getTypedRuleContext(BlockContext::class, $index);
	    }

	    /**
	     * @return array<TerminalNode>|TerminalNode|null
	     */
	    public function ELSE(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTokens(GolampiParser::ELSE);
	    	}

	        return $this->getToken(GolampiParser::ELSE, $index);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitIfElseIfElse($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class SwitchStatementContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GolampiParser::RULE_switchStatement;
	    }

	    public function SWITCH(): ?TerminalNode
	    {
	        return $this->getToken(GolampiParser::SWITCH, 0);
	    }

	    public function expression(): ?ExpressionContext
	    {
	    	return $this->getTypedRuleContext(ExpressionContext::class, 0);
	    }

	    /**
	     * @return array<CaseClauseContext>|CaseClauseContext|null
	     */
	    public function caseClause(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTypedRuleContexts(CaseClauseContext::class);
	    	}

	        return $this->getTypedRuleContext(CaseClauseContext::class, $index);
	    }

	    public function defaultClause(): ?DefaultClauseContext
	    {
	    	return $this->getTypedRuleContext(DefaultClauseContext::class, 0);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitSwitchStatement($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class CaseClauseContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GolampiParser::RULE_caseClause;
	    }

	    public function CASE(): ?TerminalNode
	    {
	        return $this->getToken(GolampiParser::CASE, 0);
	    }

	    public function expressionList(): ?ExpressionListContext
	    {
	    	return $this->getTypedRuleContext(ExpressionListContext::class, 0);
	    }

	    /**
	     * @return array<StatementContext>|StatementContext|null
	     */
	    public function statement(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTypedRuleContexts(StatementContext::class);
	    	}

	        return $this->getTypedRuleContext(StatementContext::class, $index);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitCaseClause($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class DefaultClauseContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GolampiParser::RULE_defaultClause;
	    }

	    public function DEFAULT(): ?TerminalNode
	    {
	        return $this->getToken(GolampiParser::DEFAULT, 0);
	    }

	    /**
	     * @return array<StatementContext>|StatementContext|null
	     */
	    public function statement(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTypedRuleContexts(StatementContext::class);
	    	}

	        return $this->getTypedRuleContext(StatementContext::class, $index);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitDefaultClause($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class ForStatementContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GolampiParser::RULE_forStatement;
	    }
	 
		public function copyFrom(ParserRuleContext $context): void
		{
			parent::copyFrom($context);

		}
	}

	class ForTraditionalContext extends ForStatementContext
	{
		public function __construct(ForStatementContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    public function FOR(): ?TerminalNode
	    {
	        return $this->getToken(GolampiParser::FOR, 0);
	    }

	    public function forClause(): ?ForClauseContext
	    {
	    	return $this->getTypedRuleContext(ForClauseContext::class, 0);
	    }

	    public function block(): ?BlockContext
	    {
	    	return $this->getTypedRuleContext(BlockContext::class, 0);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitForTraditional($this);
		    }

			return $visitor->visitChildren($this);
		}
	}

	class ForWhileContext extends ForStatementContext
	{
		public function __construct(ForStatementContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    public function FOR(): ?TerminalNode
	    {
	        return $this->getToken(GolampiParser::FOR, 0);
	    }

	    public function expression(): ?ExpressionContext
	    {
	    	return $this->getTypedRuleContext(ExpressionContext::class, 0);
	    }

	    public function block(): ?BlockContext
	    {
	    	return $this->getTypedRuleContext(BlockContext::class, 0);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitForWhile($this);
		    }

			return $visitor->visitChildren($this);
		}
	}

	class ForInfiniteContext extends ForStatementContext
	{
		public function __construct(ForStatementContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    public function FOR(): ?TerminalNode
	    {
	        return $this->getToken(GolampiParser::FOR, 0);
	    }

	    public function block(): ?BlockContext
	    {
	    	return $this->getTypedRuleContext(BlockContext::class, 0);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitForInfinite($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class ForClauseContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GolampiParser::RULE_forClause;
	    }

	    public function forInit(): ?ForInitContext
	    {
	    	return $this->getTypedRuleContext(ForInitContext::class, 0);
	    }

	    public function forPost(): ?ForPostContext
	    {
	    	return $this->getTypedRuleContext(ForPostContext::class, 0);
	    }

	    public function expression(): ?ExpressionContext
	    {
	    	return $this->getTypedRuleContext(ExpressionContext::class, 0);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitForClause($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class ForInitContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GolampiParser::RULE_forInit;
	    }

	    public function varDeclaration(): ?VarDeclarationContext
	    {
	    	return $this->getTypedRuleContext(VarDeclarationContext::class, 0);
	    }

	    public function shortVarDeclaration(): ?ShortVarDeclarationContext
	    {
	    	return $this->getTypedRuleContext(ShortVarDeclarationContext::class, 0);
	    }

	    public function assignment(): ?AssignmentContext
	    {
	    	return $this->getTypedRuleContext(AssignmentContext::class, 0);
	    }

	    public function incDecStatement(): ?IncDecStatementContext
	    {
	    	return $this->getTypedRuleContext(IncDecStatementContext::class, 0);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitForInit($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class ForPostContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GolampiParser::RULE_forPost;
	    }

	    public function assignment(): ?AssignmentContext
	    {
	    	return $this->getTypedRuleContext(AssignmentContext::class, 0);
	    }

	    public function incDecStatement(): ?IncDecStatementContext
	    {
	    	return $this->getTypedRuleContext(IncDecStatementContext::class, 0);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitForPost($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class BreakStatementContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GolampiParser::RULE_breakStatement;
	    }

	    public function BREAK(): ?TerminalNode
	    {
	        return $this->getToken(GolampiParser::BREAK, 0);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitBreakStatement($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class ContinueStatementContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GolampiParser::RULE_continueStatement;
	    }

	    public function CONTINUE(): ?TerminalNode
	    {
	        return $this->getToken(GolampiParser::CONTINUE, 0);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitContinueStatement($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class ReturnStatementContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GolampiParser::RULE_returnStatement;
	    }

	    public function RETURN(): ?TerminalNode
	    {
	        return $this->getToken(GolampiParser::RETURN, 0);
	    }

	    public function expressionList(): ?ExpressionListContext
	    {
	    	return $this->getTypedRuleContext(ExpressionListContext::class, 0);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitReturnStatement($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class BlockContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GolampiParser::RULE_block;
	    }

	    /**
	     * @return array<DeclarationContext>|DeclarationContext|null
	     */
	    public function declaration(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTypedRuleContexts(DeclarationContext::class);
	    	}

	        return $this->getTypedRuleContext(DeclarationContext::class, $index);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitBlock($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class ExpressionStatementContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GolampiParser::RULE_expressionStatement;
	    }

	    public function expression(): ?ExpressionContext
	    {
	    	return $this->getTypedRuleContext(ExpressionContext::class, 0);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitExpressionStatement($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class ExpressionContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GolampiParser::RULE_expression;
	    }
	 
		public function copyFrom(ParserRuleContext $context): void
		{
			parent::copyFrom($context);

		}
	}

	class NonTernaryExprContext extends ExpressionContext
	{
		public function __construct(ExpressionContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    public function logicalOr(): ?LogicalOrContext
	    {
	    	return $this->getTypedRuleContext(LogicalOrContext::class, 0);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitNonTernaryExpr($this);
		    }

			return $visitor->visitChildren($this);
		}
	}

	class TernaryExprContext extends ExpressionContext
	{
		public function __construct(ExpressionContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    public function logicalOr(): ?LogicalOrContext
	    {
	    	return $this->getTypedRuleContext(LogicalOrContext::class, 0);
	    }

	    /**
	     * @return array<ExpressionContext>|ExpressionContext|null
	     */
	    public function expression(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTypedRuleContexts(ExpressionContext::class);
	    	}

	        return $this->getTypedRuleContext(ExpressionContext::class, $index);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitTernaryExpr($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class LogicalOrContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GolampiParser::RULE_logicalOr;
	    }

	    /**
	     * @return array<LogicalAndContext>|LogicalAndContext|null
	     */
	    public function logicalAnd(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTypedRuleContexts(LogicalAndContext::class);
	    	}

	        return $this->getTypedRuleContext(LogicalAndContext::class, $index);
	    }

	    /**
	     * @return array<TerminalNode>|TerminalNode|null
	     */
	    public function OR(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTokens(GolampiParser::OR);
	    	}

	        return $this->getToken(GolampiParser::OR, $index);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitLogicalOr($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class LogicalAndContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GolampiParser::RULE_logicalAnd;
	    }

	    /**
	     * @return array<EqualityContext>|EqualityContext|null
	     */
	    public function equality(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTypedRuleContexts(EqualityContext::class);
	    	}

	        return $this->getTypedRuleContext(EqualityContext::class, $index);
	    }

	    /**
	     * @return array<TerminalNode>|TerminalNode|null
	     */
	    public function AND(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTokens(GolampiParser::AND);
	    	}

	        return $this->getToken(GolampiParser::AND, $index);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitLogicalAnd($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class EqualityContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GolampiParser::RULE_equality;
	    }

	    /**
	     * @return array<RelationalContext>|RelationalContext|null
	     */
	    public function relational(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTypedRuleContexts(RelationalContext::class);
	    	}

	        return $this->getTypedRuleContext(RelationalContext::class, $index);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitEquality($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class RelationalContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GolampiParser::RULE_relational;
	    }

	    /**
	     * @return array<AdditiveContext>|AdditiveContext|null
	     */
	    public function additive(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTypedRuleContexts(AdditiveContext::class);
	    	}

	        return $this->getTypedRuleContext(AdditiveContext::class, $index);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitRelational($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class AdditiveContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GolampiParser::RULE_additive;
	    }

	    /**
	     * @return array<MultiplicativeContext>|MultiplicativeContext|null
	     */
	    public function multiplicative(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTypedRuleContexts(MultiplicativeContext::class);
	    	}

	        return $this->getTypedRuleContext(MultiplicativeContext::class, $index);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitAdditive($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class MultiplicativeContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GolampiParser::RULE_multiplicative;
	    }

	    /**
	     * @return array<UnaryContext>|UnaryContext|null
	     */
	    public function unary(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTypedRuleContexts(UnaryContext::class);
	    	}

	        return $this->getTypedRuleContext(UnaryContext::class, $index);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitMultiplicative($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class UnaryContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GolampiParser::RULE_unary;
	    }
	 
		public function copyFrom(ParserRuleContext $context): void
		{
			parent::copyFrom($context);

		}
	}

	class AddressOfContext extends UnaryContext
	{
		public function __construct(UnaryContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    public function ID(): ?TerminalNode
	    {
	        return $this->getToken(GolampiParser::ID, 0);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitAddressOf($this);
		    }

			return $visitor->visitChildren($this);
		}
	}

	class NegativeUnaryContext extends UnaryContext
	{
		public function __construct(UnaryContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    public function unary(): ?UnaryContext
	    {
	    	return $this->getTypedRuleContext(UnaryContext::class, 0);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitNegativeUnary($this);
		    }

			return $visitor->visitChildren($this);
		}
	}

	class NotUnaryContext extends UnaryContext
	{
		public function __construct(UnaryContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    public function unary(): ?UnaryContext
	    {
	    	return $this->getTypedRuleContext(UnaryContext::class, 0);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitNotUnary($this);
		    }

			return $visitor->visitChildren($this);
		}
	}

	class DereferenceContext extends UnaryContext
	{
		public function __construct(UnaryContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    public function unary(): ?UnaryContext
	    {
	    	return $this->getTypedRuleContext(UnaryContext::class, 0);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitDereference($this);
		    }

			return $visitor->visitChildren($this);
		}
	}

	class PrimaryUnaryContext extends UnaryContext
	{
		public function __construct(UnaryContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    public function primary(): ?PrimaryContext
	    {
	    	return $this->getTypedRuleContext(PrimaryContext::class, 0);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitPrimaryUnary($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class PrimaryContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GolampiParser::RULE_primary;
	    }
	 
		public function copyFrom(ParserRuleContext $context): void
		{
			parent::copyFrom($context);

		}
	}

	class FloatLiteralContext extends PrimaryContext
	{
		public function __construct(PrimaryContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    public function FLOAT32(): ?TerminalNode
	    {
	        return $this->getToken(GolampiParser::FLOAT32, 0);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitFloatLiteral($this);
		    }

			return $visitor->visitChildren($this);
		}
	}

	class GroupedExpressionContext extends PrimaryContext
	{
		public function __construct(PrimaryContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    public function expression(): ?ExpressionContext
	    {
	    	return $this->getTypedRuleContext(ExpressionContext::class, 0);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitGroupedExpression($this);
		    }

			return $visitor->visitChildren($this);
		}
	}

	class FalseLiteralContext extends PrimaryContext
	{
		public function __construct(PrimaryContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    public function FALSE(): ?TerminalNode
	    {
	        return $this->getToken(GolampiParser::FALSE, 0);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitFalseLiteral($this);
		    }

			return $visitor->visitChildren($this);
		}
	}

	class ArrayAccessContext extends PrimaryContext
	{
		public function __construct(PrimaryContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    public function ID(): ?TerminalNode
	    {
	        return $this->getToken(GolampiParser::ID, 0);
	    }

	    /**
	     * @return array<ExpressionContext>|ExpressionContext|null
	     */
	    public function expression(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTypedRuleContexts(ExpressionContext::class);
	    	}

	        return $this->getTypedRuleContext(ExpressionContext::class, $index);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitArrayAccess($this);
		    }

			return $visitor->visitChildren($this);
		}
	}

	class IdentifierContext extends PrimaryContext
	{
		public function __construct(PrimaryContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    public function ID(): ?TerminalNode
	    {
	        return $this->getToken(GolampiParser::ID, 0);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitIdentifier($this);
		    }

			return $visitor->visitChildren($this);
		}
	}

	class StringLiteralContext extends PrimaryContext
	{
		public function __construct(PrimaryContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    public function STRING(): ?TerminalNode
	    {
	        return $this->getToken(GolampiParser::STRING, 0);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitStringLiteral($this);
		    }

			return $visitor->visitChildren($this);
		}
	}

	class TrueLiteralContext extends PrimaryContext
	{
		public function __construct(PrimaryContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    public function TRUE(): ?TerminalNode
	    {
	        return $this->getToken(GolampiParser::TRUE, 0);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitTrueLiteral($this);
		    }

			return $visitor->visitChildren($this);
		}
	}

	class InnerArrayLiteralContext extends PrimaryContext
	{
		public function __construct(PrimaryContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    public function expressionList(): ?ExpressionListContext
	    {
	    	return $this->getTypedRuleContext(ExpressionListContext::class, 0);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitInnerArrayLiteral($this);
		    }

			return $visitor->visitChildren($this);
		}
	}

	class NilLiteralContext extends PrimaryContext
	{
		public function __construct(PrimaryContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    public function NIL(): ?TerminalNode
	    {
	        return $this->getToken(GolampiParser::NIL, 0);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitNilLiteral($this);
		    }

			return $visitor->visitChildren($this);
		}
	}

	class ArrayLiteralExprContext extends PrimaryContext
	{
		public function __construct(PrimaryContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    public function arrayLiteral(): ?ArrayLiteralContext
	    {
	    	return $this->getTypedRuleContext(ArrayLiteralContext::class, 0);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitArrayLiteralExpr($this);
		    }

			return $visitor->visitChildren($this);
		}
	}

	class IntLiteralContext extends PrimaryContext
	{
		public function __construct(PrimaryContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    public function INT32(): ?TerminalNode
	    {
	        return $this->getToken(GolampiParser::INT32, 0);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitIntLiteral($this);
		    }

			return $visitor->visitChildren($this);
		}
	}

	class FunctionCallContext extends PrimaryContext
	{
		public function __construct(PrimaryContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    /**
	     * @return array<TerminalNode>|TerminalNode|null
	     */
	    public function ID(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTokens(GolampiParser::ID);
	    	}

	        return $this->getToken(GolampiParser::ID, $index);
	    }

	    public function argumentList(): ?ArgumentListContext
	    {
	    	return $this->getTypedRuleContext(ArgumentListContext::class, 0);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitFunctionCall($this);
		    }

			return $visitor->visitChildren($this);
		}
	}

	class RuneLiteralContext extends PrimaryContext
	{
		public function __construct(PrimaryContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    public function RUNE(): ?TerminalNode
	    {
	        return $this->getToken(GolampiParser::RUNE, 0);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitRuneLiteral($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class ArrayLiteralContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GolampiParser::RULE_arrayLiteral;
	    }
	 
		public function copyFrom(ParserRuleContext $context): void
		{
			parent::copyFrom($context);

		}
	}

	class FixedArrayLiteralNodeContext extends ArrayLiteralContext
	{
		public function __construct(ArrayLiteralContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    public function expression(): ?ExpressionContext
	    {
	    	return $this->getTypedRuleContext(ExpressionContext::class, 0);
	    }

	    public function type(): ?TypeContext
	    {
	    	return $this->getTypedRuleContext(TypeContext::class, 0);
	    }

	    public function expressionList(): ?ExpressionListContext
	    {
	    	return $this->getTypedRuleContext(ExpressionListContext::class, 0);
	    }

	    public function innerLiteralList(): ?InnerLiteralListContext
	    {
	    	return $this->getTypedRuleContext(InnerLiteralListContext::class, 0);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitFixedArrayLiteralNode($this);
		    }

			return $visitor->visitChildren($this);
		}
	}

	class SliceLiteralNodeContext extends ArrayLiteralContext
	{
		public function __construct(ArrayLiteralContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    public function type(): ?TypeContext
	    {
	    	return $this->getTypedRuleContext(TypeContext::class, 0);
	    }

	    public function expressionList(): ?ExpressionListContext
	    {
	    	return $this->getTypedRuleContext(ExpressionListContext::class, 0);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitSliceLiteralNode($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class InnerLiteralListContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GolampiParser::RULE_innerLiteralList;
	    }

	    /**
	     * @return array<InnerLiteralContext>|InnerLiteralContext|null
	     */
	    public function innerLiteral(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTypedRuleContexts(InnerLiteralContext::class);
	    	}

	        return $this->getTypedRuleContext(InnerLiteralContext::class, $index);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitInnerLiteralList($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class InnerLiteralContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GolampiParser::RULE_innerLiteral;
	    }

	    public function expressionList(): ?ExpressionListContext
	    {
	    	return $this->getTypedRuleContext(ExpressionListContext::class, 0);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitInnerLiteral($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class ArgumentListContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GolampiParser::RULE_argumentList;
	    }

	    /**
	     * @return array<ArgumentContext>|ArgumentContext|null
	     */
	    public function argument(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTypedRuleContexts(ArgumentContext::class);
	    	}

	        return $this->getTypedRuleContext(ArgumentContext::class, $index);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitArgumentList($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class ArgumentContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GolampiParser::RULE_argument;
	    }
	 
		public function copyFrom(ParserRuleContext $context): void
		{
			parent::copyFrom($context);

		}
	}

	class AddressArgumentContext extends ArgumentContext
	{
		public function __construct(ArgumentContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    public function ID(): ?TerminalNode
	    {
	        return $this->getToken(GolampiParser::ID, 0);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitAddressArgument($this);
		    }

			return $visitor->visitChildren($this);
		}
	}

	class ExpressionArgumentContext extends ArgumentContext
	{
		public function __construct(ArgumentContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    public function expression(): ?ExpressionContext
	    {
	    	return $this->getTypedRuleContext(ExpressionContext::class, 0);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitExpressionArgument($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class TypeContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GolampiParser::RULE_type;
	    }
	 
		public function copyFrom(ParserRuleContext $context): void
		{
			parent::copyFrom($context);

		}
	}

	class Float32TypeContext extends TypeContext
	{
		public function __construct(TypeContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    public function FLOAT32_TYPE(): ?TerminalNode
	    {
	        return $this->getToken(GolampiParser::FLOAT32_TYPE, 0);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitFloat32Type($this);
		    }

			return $visitor->visitChildren($this);
		}
	}

	class ArrayTypeContext extends TypeContext
	{
		public function __construct(TypeContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    public function expression(): ?ExpressionContext
	    {
	    	return $this->getTypedRuleContext(ExpressionContext::class, 0);
	    }

	    public function type(): ?TypeContext
	    {
	    	return $this->getTypedRuleContext(TypeContext::class, 0);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitArrayType($this);
		    }

			return $visitor->visitChildren($this);
		}
	}

	class BoolTypeContext extends TypeContext
	{
		public function __construct(TypeContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    public function BOOL_TYPE(): ?TerminalNode
	    {
	        return $this->getToken(GolampiParser::BOOL_TYPE, 0);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitBoolType($this);
		    }

			return $visitor->visitChildren($this);
		}
	}

	class RuneTypeContext extends TypeContext
	{
		public function __construct(TypeContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    public function RUNE_TYPE(): ?TerminalNode
	    {
	        return $this->getToken(GolampiParser::RUNE_TYPE, 0);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitRuneType($this);
		    }

			return $visitor->visitChildren($this);
		}
	}

	class StringTypeContext extends TypeContext
	{
		public function __construct(TypeContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    public function STRING_TYPE(): ?TerminalNode
	    {
	        return $this->getToken(GolampiParser::STRING_TYPE, 0);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitStringType($this);
		    }

			return $visitor->visitChildren($this);
		}
	}

	class SliceTypeContext extends TypeContext
	{
		public function __construct(TypeContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    public function type(): ?TypeContext
	    {
	    	return $this->getTypedRuleContext(TypeContext::class, 0);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitSliceType($this);
		    }

			return $visitor->visitChildren($this);
		}
	}

	class PointerTypeContext extends TypeContext
	{
		public function __construct(TypeContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    public function type(): ?TypeContext
	    {
	    	return $this->getTypedRuleContext(TypeContext::class, 0);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitPointerType($this);
		    }

			return $visitor->visitChildren($this);
		}
	}

	class Int32TypeContext extends TypeContext
	{
		public function __construct(TypeContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    public function INT32_TYPE(): ?TerminalNode
	    {
	        return $this->getToken(GolampiParser::INT32_TYPE, 0);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GolampiVisitor) {
			    return $visitor->visitInt32Type($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 
}