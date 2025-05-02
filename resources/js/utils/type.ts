/**Represents a list of possible validation rules that can be applied to form fields. */
export type Validators =
  | "required"
  | `min:${number}`
  | `max:${number}`
  | "email"
  | "mobile"
  | "array";

type FieldType =
  | "text"
  | "select"
  | "date"
  | "number"
  | "password"
  | "email";

export interface FormField {
  /**A unique identifier for the form field. */
  id: string;

  /** Type of the form field. */
  type?: FieldType;

  /**The label to display for the form field. */
  label?: string;

  /**List of options for 'select' type fields. */
  options?: Record<string, any>;

  /**An array of validation rules for the form field. */
  validators?: Validators[];

  /**Placeholder text for the input field. */
  placeholder?: string;

  defaultValue?: string | string[] | boolean | number | File;

  /**Minimum value for number or date fields. */
  min?: string | number | Date;

  /**Maximum value for number or date fields */
  max?: string | number | Date;

}
