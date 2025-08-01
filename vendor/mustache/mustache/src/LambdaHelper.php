<?php

/*
 * This file is part of Mustache.php.
 *
 * (c) 2010-2025 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mustache;

/**
 * Mustache Lambda Helper.
 *
 * Passed as the second argument to section lambdas (higher order sections),
 * giving them access to a `render` method for rendering a string with the
 * current context.
 */
class LambdaHelper
{
    private $mustache;
    private $context;
    private $delims;

    /**
     * Mustache Lambda Helper constructor.
     *
     * @param Engine  $mustache Mustache engine instance
     * @param Context $context  Rendering context
     * @param string  $delims   Optional custom delimiters, in the format `{{= <% %> =}}`. (default: null)
     */
    public function __construct(Engine $mustache, Context $context, $delims = null)
    {
        $this->mustache = $mustache;
        $this->context  = $context;
        $this->delims   = $delims;
    }

    /**
     * Render a string as a Mustache template with the current rendering context.
     *
     * @param string $string
     *
     * @return string Rendered template
     */
    public function render($string)
    {
        $value = $this->mustache
            ->loadLambda((string) $string, $this->delims)
            ->renderInternal($this->context);

        return $this->mustache->getDoubleRenderLambdas() ? $value : $this->preventRender($value);
    }

    /**
     * Prevent rendering of a string as a Mustache template.
     *
     * This is useful for returning a raw string from a lambda without processing it as a Mustache template.
     *
     * @see RenderedString
     *
     * @param string $value The raw string value to return
     *
     * @return RenderedString A RenderedString instance containing the raw value
     */
    public function preventRender($value)
    {
        return new RenderedString($value);
    }

    /**
     * Render a string as a Mustache template with the current rendering context.
     *
     * @param string $string
     *
     * @return string Rendered template
     */
    public function __invoke($string)
    {
        return $this->render($string);
    }

    /**
     * Get a Lambda Helper with custom delimiters.
     *
     * @param string $delims Custom delimiters, in the format `{{= <% %> =}}`
     *
     * @return LambdaHelper
     */
    public function withDelimiters($delims)
    {
        return new self($this->mustache, $this->context, $delims);
    }
}
