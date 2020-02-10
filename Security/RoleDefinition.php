<?php
declare(strict_types=1);

namespace Bungle\FrameworkBundle\Security;

/**
 * Definition of a role,
 *
 * Role name contains three parts, separate by '_'.
 *
 * The first part always 'ROLE' to match the conversion of symfony.
 *
 * The second part is the group name, role will grouped.
 *
 * For each entity object, the second part is role's high value,
 * third part is state machine action name.
 */
class RoleDefinition
{
    private string $name;
    private string $title;
    private string $description;

    public function __construct(string $name, string $title, string $description)
    {
        $this->name = $name;
        $this->title = $title;
        $this->description = $description;
    }

    public static function newActionRole(String $high, String $action): String
    {
        return "ROLE_${high}_$action";
    }

    // Parse state machine action role name, returns array with two items:
    // [$high, $action].
    public function parseActionRole(String $roleName): array
    {
        return array_slice(explode('_', $roleName), 1);
    }

    /**
     * Full name of the role, begin with ROLE_
     */
    public function name(): string
    {
        return $this->name;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function description(): string
    {
        return $this->description;
    }
}
