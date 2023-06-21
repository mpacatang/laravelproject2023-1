import {IModel} from "@js/types/models/models";
import {IAdmin} from "@js/types/models/admin";
import {IModule} from "@js/types/models/module";

export interface IIStep1 extends IModel {
    can_next: boolean,
    php_version: string,
    need_to_update_php: boolean,
    curl_enable: boolean,
    bootstrap_permission: boolean,
    storage_permission: boolean,
    env_write_permission: boolean,
}

export interface IIStep2 extends IModel {
    can_next: boolean,
    reason: string,
    db_host: string,
    db_name: string,
    db_username: string,
    db_password: string,
    database_valid: boolean
}

export interface IIStep3 extends IModel {
    can_next: boolean,
    migrations: {
        migration: string,
        pending: boolean
    }[]
}

export interface IIStep4 extends IModel {
    can_next: boolean,
    admin: IAdmin
}

export interface IIStep5 extends IModel {
    can_next: boolean,
    version: string,
}
