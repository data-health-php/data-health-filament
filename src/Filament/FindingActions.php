<?php

declare(strict_types=1);

namespace DataHealth\Filament\Filament;

use DataHealth\DataHealthManager;
use DataHealth\Enums\RecordStatus;
use DataHealth\Filament\Support\AssigneeTypes;
use DataHealth\Filament\Support\FindingRecordAction;
use DataHealth\Models\FindingRecord;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;

final class FindingActions
{
    public const VERIFY_DESCRIPTION = 'Check if the issue is fixed';

    public const RESOLVE_DESCRIPTION = 'Fix the issue automatically';

    public const MARK_AS_RESOLVED_DESCRIPTION = 'Assume the issue is resolved without checking';

    public const IGNORE_DESCRIPTION = 'Ignore this single issue forever';

    public static function assign(): Action
    {
        return Action::make('assign')
            ->icon(Heroicon::OutlinedUserPlus)
            ->modalHeading('Assign finding')
            ->schema([AssigneeSelect::make()])
            ->fillForm(fn (FindingRecord $record): array => [
                'assignee_type' => $record->assignee_type,
                'assignee_id' => $record->assignee_id,
            ])
            ->visible(fn (): bool => app(AssigneeTypes::class)->all() !== [])
            ->successNotificationTitle('Finding assignment updated')
            ->action(function (FindingRecord $record, array $data, Action $action): void {
                $record->forceFill([
                    'assignee_type' => $data['assignee_type'] ?? null,
                    'assignee_id' => $data['assignee_id'] ?? null,
                ])->save();

                $action->success();
            });
    }

    public static function verify(): Action
    {
        return Action::make('verify')
            ->icon(Heroicon::OutlinedShieldCheck)
            ->color('info')
            ->tooltip(fn (FindingRecord $record): string => FindingRecordAction::methodDescription($record, 'verify', self::VERIFY_DESCRIPTION))
            ->visible(fn (FindingRecord $record): bool => $record->status === RecordStatus::Active && FindingRecordAction::canVerify($record))
            ->successNotificationTitle('Finding no longer applies')
            ->failureNotificationTitle('Finding still applies')
            ->action(function (FindingRecord $record, Action $action): void {
                app(DataHealthManager::class)->verify($record)
                    ? $action->success()
                    : $action->failure();
            });
    }

    public static function resolve(): Action
    {
        return Action::make('resolve')
            ->icon(Heroicon::OutlinedCheckCircle)
            ->color('success')
            ->tooltip(fn (FindingRecord $record): string => FindingRecordAction::methodDescription($record, 'resolve', self::RESOLVE_DESCRIPTION))
            ->requiresConfirmation()
            ->modalDescription(fn (FindingRecord $record): string => FindingRecordAction::methodDescription($record, 'resolve', self::RESOLVE_DESCRIPTION))
            ->visible(fn (FindingRecord $record): bool => $record->status === RecordStatus::Active && FindingRecordAction::canResolve($record))
            ->successNotificationTitle('Finding resolved')
            ->failureNotificationTitle('Finding could not be resolved')
            ->action(function (FindingRecord $record, Action $action): void {
                app(DataHealthManager::class)->resolve($record)
                    ? $action->success()
                    : $action->failure();
            });
    }

    public static function markAsResolved(): Action
    {
        return Action::make('markAsResolved')
            ->label('Mark as resolved')
            ->icon(Heroicon::OutlinedCheckBadge)
            ->color('success')
            ->tooltip(self::MARK_AS_RESOLVED_DESCRIPTION)
            ->requiresConfirmation()
            ->modalDescription(self::MARK_AS_RESOLVED_DESCRIPTION)
            ->visible(fn (FindingRecord $record): bool => $record->status === RecordStatus::Active)
            ->successNotificationTitle('Finding marked as resolved')
            ->action(function (FindingRecord $record, Action $action): void {
                FindingRecordAction::markAsResolved($record);
                $action->success();
            });
    }

    public static function ignore(): Action
    {
        return Action::make('ignore')
            ->icon(Heroicon::OutlinedEyeSlash)
            ->color('gray')
            ->tooltip(self::IGNORE_DESCRIPTION)
            ->requiresConfirmation()
            ->modalDescription(self::IGNORE_DESCRIPTION)
            ->visible(fn (FindingRecord $record): bool => $record->status === RecordStatus::Active)
            ->successNotificationTitle('Finding ignored')
            ->action(function (FindingRecord $record, Action $action): void {
                FindingRecordAction::ignore($record);
                $action->success();
            });
    }

    public static function reopen(): Action
    {
        return Action::make('reopen')
            ->icon(Heroicon::OutlinedArrowPath)
            ->visible(fn (FindingRecord $record): bool => $record->status !== RecordStatus::Active)
            ->successNotificationTitle('Finding reopened')
            ->action(function (FindingRecord $record, Action $action): void {
                FindingRecordAction::reopen($record);
                $action->success();
            });
    }

    /** @return array<Action> */
    public static function all(): array
    {
        return [
            self::assign(),
            self::verify(),
            self::resolve(),
            self::ignore(),
            self::reopen(),
        ];
    }
}
