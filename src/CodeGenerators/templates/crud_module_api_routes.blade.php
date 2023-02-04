    $this->router()->get('', [{{$controllerClassName}}::class, 'index']);
    $this->router()->post('', [{{$controllerClassName}}::class, 'store']);
    $this->router()->get('{{{$routeResourceName}}}', [{{$controllerClassName}}::class, 'show']);
    $this->router()->addRoute(['PUT', 'PATCH'], '{{{$routeResourceName}}}', [{{$controllerClassName}}::class, 'update']);
    $this->router()->delete('{{{$routeResourceName}}}', [{{$controllerClassName}}::class, 'destroy']);
