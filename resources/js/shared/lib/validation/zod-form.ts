import type { ZodError, ZodType } from 'zod';

export function mapZodFieldErrors(error: ZodError): Record<string, string> {
    const out: Record<string, string> = {};

    for (const issue of error.issues) {
        const key = issue.path[0];

        if (typeof key === 'string' && !out[key]) {
            out[key] = issue.message;
        }
    }

    return out;
}

export function safeParseForm<T>(
    schema: ZodType<T>,
    data: unknown,
):
    | { success: true; data: T }
    | { success: false; errors: Record<string, string> } {
    const result = schema.safeParse(data);

    if (result.success) {
        return { success: true, data: result.data };
    }

    return { success: false, errors: mapZodFieldErrors(result.error) };
}