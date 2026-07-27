<?php

declare(strict_types=1);

namespace App\Http;

use App\Contracts\Http\HttpKernelInterface;
use App\Contracts\Http\MiddlewareDispatcherInterface;
use App\Contracts\Http\MiddlewareRegistryInterface;
use App\Contracts\Http\MiddlewareResolverInterface;
use App\Contracts\Http\PipelineInterface;
use App\Contracts\Http\ResponseEmitterInterface;

class HttpKernel implements HttpKernelInterface
{
    private ?PipelineInterface $pipeline = null;

    private ?MiddlewareDispatcherInterface $dispatcher = null;

    private ?MiddlewareResolverInterface $resolver = null;

    private ?MiddlewareRegistryInterface $registry = null;

    private ?ResponseEmitterInterface $emitter = null;

    public function handle(RequestContext $context): RequestContext
    {
        if ($this->pipeline !== null) {
            $this->pipeline->send($context);
        }

        if ($this->dispatcher !== null && $this->resolver !== null) {
            $middleware = $this->resolver->resolve([]);
            $this->dispatcher->dispatch($context, $middleware);
        }

        return $context;
    }

    public function terminate(RequestContext $context): void
    {
        if ($this->emitter !== null) {
            $this->emitter->emit($context->getResponse());
        }
    }

    public function setPipeline(PipelineInterface $pipeline): self
    {
        $this->pipeline = $pipeline;
        return $this;
    }

    public function setDispatcher(MiddlewareDispatcherInterface $dispatcher): self
    {
        $this->dispatcher = $dispatcher;
        return $this;
    }

    public function setResolver(MiddlewareResolverInterface $resolver): self
    {
        $this->resolver = $resolver;
        return $this;
    }

    public function setRegistry(MiddlewareRegistryInterface $registry): self
    {
        $this->registry = $registry;
        return $this;
    }

    public function setEmitter(ResponseEmitterInterface $emitter): self
    {
        $this->emitter = $emitter;
        return $this;
    }

    public function getPipeline(): ?PipelineInterface
    {
        return $this->pipeline;
    }

    public function getDispatcher(): ?MiddlewareDispatcherInterface
    {
        return $this->dispatcher;
    }

    public function getResolver(): ?MiddlewareResolverInterface
    {
        return $this->resolver;
    }

    public function getRegistry(): ?MiddlewareRegistryInterface
    {
        return $this->registry;
    }

    public function getEmitter(): ?ResponseEmitterInterface
    {
        return $this->emitter;
    }
}
