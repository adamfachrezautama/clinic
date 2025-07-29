<?php

namespace App\Enums;

enum PermissionEnum: string
{
    // Doctor permissions
    case VIEW_DOCTORS   = 'view doctors';
    case CREATE_DOCTORS = 'create doctors';
    case EDIT_DOCTORS   = 'edit doctors';
    case DELETE_DOCTORS = 'delete doctors';

    // Patient permissions
    case VIEW_PATIENTS   = 'view patients';
    case CREATE_PATIENTS = 'create patients';
    case EDIT_PATIENTS   = 'edit patients';
    case DELETE_PATIENTS = 'delete patients';

    // Specialization permissions
    case VIEW_SPECIALIZATIONS   = 'view specializations';
    case CREATE_SPECIALIZATIONS = 'create specializations';
    case EDIT_SPECIALIZATIONS   = 'edit specializations';
    case DELETE_SPECIALIZATIONS = 'delete specializations';

    // Transaction permissions
    case VIEW_TRANSACTIONS   = 'view transactions';
    case CREATE_TRANSACTIONS = 'create transactions';
    case EDIT_TRANSACTIONS   = 'edit transactions';
    case DELETE_TRANSACTIONS = 'delete transactions';

    // Agora permissions
    case AGORA_VIEW_CALL_HISTORY = 'agora.view call history';
    case AGORA_INITIATE_CALL     = 'agora.initiate call';
    case AGORA_JOIN_CALL         = 'agora.join call';
    case AGORA_END_CALL          = 'agora.end call';
    case AGORA_MUTE_UNMUTE_CALL  = 'agora.mute/unmute call';

    // Get all permissions
    public static function all(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }

    public static function webPermissions(): array
    {
        return [
            self::VIEW_DOCTORS,
            self::CREATE_DOCTORS,
            self::EDIT_DOCTORS,
            self::DELETE_DOCTORS,
            self::VIEW_PATIENTS,
            self::CREATE_PATIENTS,
            self::EDIT_PATIENTS,
            self::DELETE_PATIENTS,
            self::VIEW_SPECIALIZATIONS,
            self::CREATE_SPECIALIZATIONS,
            self::EDIT_SPECIALIZATIONS,
            self::DELETE_SPECIALIZATIONS,
            self::VIEW_TRANSACTIONS,
            self::CREATE_TRANSACTIONS,
            self::EDIT_TRANSACTIONS,
            self::DELETE_TRANSACTIONS,
        ];
    }

    public static function apiPermissions(): array
    {
        return [
            ...self::webPermissions(),
            self::AGORA_VIEW_CALL_HISTORY,
            self::AGORA_INITIATE_CALL,
            self::AGORA_JOIN_CALL,
            self::AGORA_END_CALL,
        ];
    }

    public static function doctorRolePermissions(): array
    {
        return [
            self::VIEW_DOCTORS,
            self::CREATE_DOCTORS,
            self::EDIT_DOCTORS,
            self::AGORA_JOIN_CALL,
            self::AGORA_END_CALL,
            self::VIEW_PATIENTS,
            self::CREATE_SPECIALIZATIONS,
            self::EDIT_SPECIALIZATIONS,
            self::VIEW_SPECIALIZATIONS,
        ];
    }

    public static function patientRolePermissions(): array
    {
        return [
            self::VIEW_PATIENTS,
            self::CREATE_PATIENTS,
            self::EDIT_PATIENTS,
            self::VIEW_DOCTORS,
            self::VIEW_SPECIALIZATIONS,
            self::CREATE_SPECIALIZATIONS,
            self::EDIT_SPECIALIZATIONS,
            self::VIEW_TRANSACTIONS,
            self::CREATE_TRANSACTIONS,
            self::AGORA_VIEW_CALL_HISTORY,
            self::AGORA_INITIATE_CALL,
            self::AGORA_JOIN_CALL,
            self::AGORA_END_CALL,
        ];
    }

    public static function agoraPermissions(): array
    {
        return [
            self::AGORA_VIEW_CALL_HISTORY,
            self::AGORA_INITIATE_CALL,
            self::AGORA_JOIN_CALL,
            self::AGORA_END_CALL,
            self::AGORA_MUTE_UNMUTE_CALL,
        ];
    }

    /**
     * Convert array of enum to array of values (strings)
     */
    public static function toValues(array $permissions): array
    {
        return array_map(fn($p) => $p->value, $permissions);
    }
}
