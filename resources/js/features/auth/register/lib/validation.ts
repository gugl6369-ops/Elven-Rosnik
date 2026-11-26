import { z } from 'zod';

import type { RegisterFormValues } from '@/entities/auth';
import type { ContinueRegisterFormValues } from '@/entities/user';
import {
    emailValid,
    mapZodFieldErrors,
    passwordValid,
    safeParseForm,
    usernameValid,
} from '@/shared/lib';

export { mapZodFieldErrors, safeParseForm };

const registrationSchemaRaw = z
    .object({
        email: z
            .string()
            .trim()
            .min(1, 'Укажите почту')
            .pipe(emailValid),
        password: passwordValid,
        password_confirmation: z.string().min(1, 'Повторите пароль'),
    })
    .refine((data) => data.password === data.password_confirmation, {
        message: 'Пароли не совпадают',
        path: ['password_confirmation'],
    }) satisfies z.ZodType<RegisterFormValues>;

export const registerSchema = registrationSchemaRaw;

const continueRegisterSchemaRaw = z.object({
    username: usernameValid,
    name: z
        .string()
        .trim()
        .min(1, 'Укажите имя')
        .max(255, 'Максимум 255 символов'),
    status: z
        .string()
        .trim()
        .min(1, 'Укажите статус')
        .max(32, 'Максимум 32 символа'),
}) satisfies z.ZodType<ContinueRegisterFormValues>;

export const continueRegisterSchema = continueRegisterSchemaRaw;

export type { ContinueRegisterFormValues, RegisterFormValues };