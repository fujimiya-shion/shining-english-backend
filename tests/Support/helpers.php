<?php

use Filament\Schemas\Schema;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Foundation\Auth\User as AuthUser;
use Illuminate\Http\JsonResponse;

if (! function_exists('assertRepositoryContract')) {
    /**
     * Assert a repository implements its interface and stores the given model instance.
     */
    function assertRepositoryContract(object $repository, string $interface, object $model): void
    {
        expect($repository)->toBeInstanceOf($interface);

        $actualModel = (fn () => $this->model)->call($repository);
        expect($actualModel)->toBe($model);
    }
}

if (! function_exists('assertServiceContract')) {
    function assertServiceContract(object $service, string $interface, object $repository): void
    {
        expect($service)->toBeInstanceOf($interface);

        $actialRepository = (fn () => $this->repository)->call($service);
        expect($actialRepository)->toBe($repository);
    }
}

if (! function_exists('assertJsonResponsePayload')) {
    /**
     * Assert JsonResponse status and payload keys/values.
     *
     * @param  array<string, mixed>  $expectedPayload
     */
    function assertJsonResponsePayload(JsonResponse $response, int $statusCode, array $expectedPayload): void
    {
        expect($response)->toBeInstanceOf(JsonResponse::class);
        expect($response->getStatusCode())->toBe($statusCode);
        expect($response->getData(true))->toMatchArray($expectedPayload);
    }
}

if (! function_exists('invokeProtectedMethod')) {
    /**
     * Invoke a protected/private method on an object.
     *
     * @param  array<int, mixed>  $arguments
     */
    function invokeProtectedMethod(object $object, string $method, array $arguments = []): mixed
    {
        $reflection = new ReflectionMethod($object, $method);
        $reflection->setAccessible(true);

        return $reflection->invokeArgs($object, $arguments);
    }
}

if (! function_exists('getProtectedPropertyValue')) {
    function getProtectedPropertyValue(object $object, string $property): mixed
    {
        $reflection = new ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }
}

if (! function_exists('makePermissionSpyUser')) {
    /**
     * Build an AuthUser that records the last permission checked.
     *
     * @param  array<int, string>  $allowedPermissions
     */
    function makePermissionSpyUser(array $allowedPermissions = []): AuthUser
    {
        return new class($allowedPermissions) extends AuthUser
        {
            /**
             * @var array<int, string>
             */
            public array $allowedPermissions;

            public ?string $lastAbility = null;

            /**
             * @param  array<int, string>  $allowedPermissions
             */
            public function __construct(array $allowedPermissions = [])
            {
                parent::__construct([]);

                $this->allowedPermissions = $allowedPermissions;
            }

            public function can($ability, $arguments = []): bool
            {
                $this->lastAbility = $ability;

                return in_array($ability, $this->allowedPermissions, true);
            }
        };
    }
}

if (! function_exists('assertPolicyChecksPermission')) {
    /**
     * Assert policy calls can() with the expected permission and allows it.
     *
     * @param  array<int, mixed>  $arguments
     */
    function assertPolicyChecksPermission(object $policy, string $method, string $permission, array $arguments = []): void
    {
        $user = makePermissionSpyUser([$permission]);

        $result = $policy->{$method}($user, ...$arguments);

        expect($user->lastAbility)->toBe($permission);
        expect($result)->toBeTrue();
    }
}

if (! function_exists('makeSchema')) {
    function makeSchema(): Schema
    {
        return Schema::make();
    }
}

if (! function_exists('schemaComponentMap')) {
    /**
     * @return array<string, object>
     */
    function schemaComponentMap(Schema $schema): array
    {
        $components = $schema->getComponents(withActions: false, withHidden: true);
        $map = [];

        foreach ($components as $component) {
            if (! method_exists($component, 'getName')) {
                continue;
            }

            $name = $component->getName();
            if ($name === null) {
                continue;
            }

            $map[$name] = $component;
        }

        return $map;
    }
}

if (! function_exists('makeTable')) {
    function makeTable(): Table
    {
        $livewire = Mockery::mock(HasTable::class);

        return Table::make($livewire);
    }
}

if (! function_exists('tableColumnNames')) {
    /**
     * @return array<int, string>
     */
    function tableColumnNames(Table $table): array
    {
        return array_keys($table->getColumns());
    }
}

if (! function_exists('actionClassList')) {
    /**
     * @return array<int, class-string>
     */
    function actionClassList(array $actions): array
    {
        return array_values(array_map(
            fn (object $action): string => $action::class,
            $actions
        ));
    }
}
