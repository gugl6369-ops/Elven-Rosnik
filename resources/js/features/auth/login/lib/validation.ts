import { z } from 'zod';

import type { LoginFormValues } from '@/entities/auth';
import { mapZodFieldErrors, safeParseForm } from '@/shared/lib';

export { mapZodFieldErrors, safeParseForm };

const loginSchemaRaw = z.object({
    username: z.string().trim().min(1, 'Укажите логин'),
    password: z.string().min(1, 'Укажите пароль'),
}) satisfies z.ZodType<LoginFormValues>;

export const loginSchema = loginSchemaRaw;

export type { LoginFormValues };