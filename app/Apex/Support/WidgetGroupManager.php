<?php

namespace App\Apex\Support;

use Illuminate\Support\Facades\Log;

class WidgetGroupManager
{
    /**
     * Widget groups
     */
    protected array $groups = [];

    /**
     * Group event listeners
     */
    protected array $groupListeners = [];

    /**
     * Add a widget to a group
     */
    public function addToGroup(string $widgetId, string $groupName): void
    {
        if (!isset($this->groups[$groupName])) {
            $this->groups[$groupName] = [];
        }

        if (!in_array($widgetId, $this->groups[$groupName])) {
            $this->groups[$groupName][] = $widgetId;

            Log::debug("APEX: Added widget to group", [
                'widget' => $widgetId,
                'group' => $groupName
            ]);
        }
    }

    /**
     * Remove a widget from a group
     */
    public function removeFromGroup(string $widgetId, string $groupName): void
    {
        if (isset($this->groups[$groupName])) {
            $this->groups[$groupName] = array_filter($this->groups[$groupName], function ($id) use ($widgetId) {
                return $id !== $widgetId;
            });

            // Remove group if empty
            if (empty($this->groups[$groupName])) {
                unset($this->groups[$groupName]);
            }

            Log::debug("APEX: Removed widget from group", [
                'widget' => $widgetId,
                'group' => $groupName
            ]);
        }
    }

    /**
     * Get all widgets in a group
     */
    public function getGroupMembers(string $groupName): array
    {
        return $this->groups[$groupName] ?? [];
    }

    /**
     * Get all groups a widget belongs to
     */
    public function getWidgetGroups(string $widgetId): array
    {
        $groups = [];

        foreach ($this->groups as $groupName => $members) {
            if (in_array($widgetId, $members)) {
                $groups[] = $groupName;
            }
        }

        return $groups;
    }

    /**
     * Check if a group exists
     */
    public function hasGroup(string $groupName): bool
    {
        return isset($this->groups[$groupName]) && !empty($this->groups[$groupName]);
    }

    /**
     * Get all groups
     */
    public function getAllGroups(): array
    {
        return $this->groups;
    }

    /**
     * Add a group event listener
     */
    public function addGroupListener(string $groupName, string $event, array $options = []): void
    {
        if (!isset($this->groupListeners[$groupName])) {
            $this->groupListeners[$groupName] = [];
        }

        $this->groupListeners[$groupName][] = [
            'event' => $event,
            'options' => $options
        ];
    }

    /**
     * Generate JavaScript to set up group listeners
     */
    public function generateGroupListenersScript(): string
    {
        if (empty($this->groups)) {
            return '';
        }

        $script = "// APEX Widget Groups\n";
        $script .= "document.addEventListener('DOMContentLoaded', function() {\n";
        $script .= "    if (!window.ApexWidgetManager) {\n";
        $script .= "        console.warn('APEX: ApexWidgetManager not found. Widget groups will not work.');\n";
        $script .= "        return;\n";
        $script .= "    }\n\n";

        foreach ($this->groups as $groupName => $widgetIds) {
            $script .= "    // Define widget group: {$groupName}\n";
            $script .= "    window.ApexWidgetManager.defineWidgetGroup('{$groupName}', " . json_encode($widgetIds) . ");\n";
        }

        // Add group listeners if any
        foreach ($this->groupListeners as $groupName => $listeners) {
            foreach ($listeners as $listener) {
                $event = $listener['event'];
                $optionsJson = json_encode($listener['options']);

                $script .= "    // Add group listener for {$groupName}:{$event}\n";
                $script .= "    window.ApexWidgetManager.addGroupListener('{$groupName}', '{$event}', {$optionsJson});\n";
            }
        }

        $script .= "});\n";

        return $script;
    }

    /**
     * Create a group with multiple widgets at once
     */
    public function createGroup(string $groupName, array $widgetIds): void
    {
        $this->groups[$groupName] = [];

        foreach ($widgetIds as $widgetId) {
            $this->addToGroup($widgetId, $groupName);
        }

        Log::debug("APEX: Created widget group", [
            'group' => $groupName,
            'widgets' => $widgetIds
        ]);
    }

    /**
     * Delete an entire group
     */
    public function deleteGroup(string $groupName): void
    {
        unset($this->groups[$groupName]);
        unset($this->groupListeners[$groupName]);

        Log::debug("APEX: Deleted widget group", ['group' => $groupName]);
    }

    /**
     * Get group statistics
     */
    public function getGroupStats(): array
    {
        $stats = [
            'total_groups' => count($this->groups),
            'total_widgets' => 0,
            'groups' => []
        ];

        foreach ($this->groups as $groupName => $members) {
            $memberCount = count($members);
            $stats['total_widgets'] += $memberCount;

            $stats['groups'][$groupName] = [
                'member_count' => $memberCount,
                'members' => $members,
                'listeners' => count($this->groupListeners[$groupName] ?? [])
            ];
        }

        $stats['avg_widgets_per_group'] = $stats['total_groups'] > 0
            ? round($stats['total_widgets'] / $stats['total_groups'], 2)
            : 0;

        return $stats;
    }

    /**
     * Check if a widget is in any group
     */
    public function isWidgetInAnyGroup(string $widgetId): bool
    {
        foreach ($this->groups as $members) {
            if (in_array($widgetId, $members)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get widgets that are not in any group
     */
    public function getUngroupedWidgets(array $allWidgetIds): array
    {
        $groupedWidgets = [];

        foreach ($this->groups as $members) {
            $groupedWidgets = array_merge($groupedWidgets, $members);
        }

        return array_diff($allWidgetIds, $groupedWidgets);
    }

    /**
     * Merge two groups
     */
    public function mergeGroups(string $sourceGroup, string $targetGroup): void
    {
        if (!$this->hasGroup($sourceGroup) || !$this->hasGroup($targetGroup)) {
            Log::warning("APEX: Cannot merge groups - one or both don't exist", [
                'source' => $sourceGroup,
                'target' => $targetGroup
            ]);
            return;
        }

        // Move all widgets from source to target
        $sourceMembers = $this->groups[$sourceGroup];

        foreach ($sourceMembers as $widgetId) {
            $this->addToGroup($widgetId, $targetGroup);
        }

        // Merge listeners
        if (isset($this->groupListeners[$sourceGroup])) {
            if (!isset($this->groupListeners[$targetGroup])) {
                $this->groupListeners[$targetGroup] = [];
            }

            $this->groupListeners[$targetGroup] = array_merge(
                $this->groupListeners[$targetGroup],
                $this->groupListeners[$sourceGroup]
            );
        }

        // Delete source group
        $this->deleteGroup($sourceGroup);

        Log::debug("APEX: Merged widget groups", [
            'source' => $sourceGroup,
            'target' => $targetGroup,
            'moved_widgets' => count($sourceMembers)
        ]);
    }

    /**
     * Clear all groups (for testing or cleanup)
     */
    public function clear(): void
    {
        $this->groups = [];
        $this->groupListeners = [];

        Log::debug("APEX: Cleared all widget groups");
    }
}
