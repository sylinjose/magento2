<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types = 1);

namespace Magento2\Sniffs\PHP;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

/**
 * Detects bad usages of ObjectManager in constructor.
 */
class AutogeneratedClassNotInConstructorSniff implements Sniff
{
    private const ERROR_CODE = 'AutogeneratedClassNotInConstructor';

    private const AUTOGENERATED_CLASS_SUFFIXES = [
        'Factory'
    ];

    /**
     * @inheritdoc
     */
    public function register()
    {
        return [T_DOUBLE_COLON];
    }

    /**
     * @inheritdoc
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        if (!$this->isClass($phpcsFile)) {
            return;
        }

        $tokens = $phpcsFile->getTokens();

        if ($tokens[$stackPtr - 1]['content'] !== 'ObjectManager'
            && $tokens[$stackPtr + 1]['content'] !== 'getInstance'
        ) {
            return;
        }

        if (!isset($tokens[$stackPtr + 4]) || $tokens[$stackPtr + 4]['code'] !== T_SEMICOLON) {
            $arrowPosition = $phpcsFile->findNext(T_OBJECT_OPERATOR, $stackPtr);
            if ($arrowPosition !== false) {
                $this->validateRequestedClass(
                    $phpcsFile,
                    $arrowPosition
                );
            }
            return;
        }

        $objectManagerVariableName = $this->getObjectManagerVariableName($phpcsFile, $stackPtr);

        if (!$objectManagerVariableName) {
            return;
        }

        $variablePosition = $phpcsFile->findNext(T_VARIABLE, $stackPtr, null, false, $objectManagerVariableName);
        if ($variablePosition) {
            $this->validateRequestedClass($phpcsFile, $phpcsFile->findNext(T_OBJECT_OPERATOR, $variablePosition));
        }
    }

    /**
     * Check if the class is instantiated via get/create method, it is autogenerated and present in constructor
     *
     * @param File $phpcsFile
     * @param int $arrowPosition
     */
    private function validateRequestedClass(File $phpcsFile, int $arrowPosition): void
    {
        $requestedClass = $this->getRequestedClass($phpcsFile, $arrowPosition);

        if (!$requestedClass
            || !$this->isClassAutogenerated($requestedClass)
            || $this->isConstructorParameter($phpcsFile, $requestedClass)
        ) {
            return;
        }

        $phpcsFile->addError(
            sprintf(
                'Class %s needs to be requested in constructor, ' .
                'otherwise compiler will not be able to find and generate this class',
                $requestedClass
            ),
            $arrowPosition,
            self::ERROR_CODE
        );
    }

    /**
     * Does the class have the suffix common for autogenerated classes e.g. Factory
     *
     * @param string $className
     * @return bool
     */
    private function isClassAutogenerated(string $className): bool
    {
        foreach (self::AUTOGENERATED_CLASS_SUFFIXES as $suffix) {
            if (substr($className, -strlen($suffix)) === $suffix) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get the variable name to which the ObjectManager::getInstance() result is assigned
     *
     * @param File $phpcsFile
     * @param int $stackPtr
     * @return string|null
     */
    private function getObjectManagerVariableName(File $phpcsFile, int $stackPtr): ?string
    {
        $matches = [];
        $found = preg_match(
            '/(\$[A-Za-z]+) ?= ?ObjectManager::getInstance\(\);/',
            $phpcsFile->getTokensAsString($stackPtr - 5, 10),
            $matches
        );

        if (!$found || !isset($matches[1])) {
            return null;
        }

        return $matches[1];
    }

    /**
     * Get class name requested from ObjectManager
     *
     * @param File $phpcsFile
     * @param int $callerPosition
     * @return string|null
     */
    private function getRequestedClass(File $phpcsFile, int $callerPosition): ?string
    {
        $matches = [];
        $found = preg_match(
            '/->(get|create)\(([A-Za-z\\\]+)::class/',
            $phpcsFile->getTokensAsString($callerPosition, $phpcsFile->findNext(T_CLOSE_PARENTHESIS, $callerPosition)),
            $matches
        );

        if (!$found || !isset($matches[2])) {
            return null;
        }

        return $matches[2];
    }

    /**
     * Does the file contain class declaration
     *
     * @param File $phpcsFile
     * @return bool
     */
    private function isClass(File $phpcsFile): bool
    {
        foreach ($phpcsFile->getTokens() as $token) {
            if ($token['code'] === T_CLASS) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get an array of constructor parameters
     *
     * @param File $phpcsFile
     * @return array
     */
    private function getConstructorParameters(File $phpcsFile): array
    {
        foreach ($phpcsFile->getTokens() as $stackPtr => $token) {
            if ($token['code'] === T_FUNCTION && $phpcsFile->getDeclarationName($stackPtr) === '__construct') {
                return $phpcsFile->getMethodParameters($stackPtr);
            }
        }
        return [];
    }

    /**
     * Is the class name present between constructor parameters
     *
     * @param File $phpcsFile
     * @param string $className
     * @return bool
     */
    private function isConstructorParameter(File $phpcsFile, string $className): bool
    {
        foreach ($this->getConstructorParameters($phpcsFile) as $parameter) {
            if (strpos($parameter['content'], $className) !== false) {
                return true;
            }
        }
        return false;
    }
}