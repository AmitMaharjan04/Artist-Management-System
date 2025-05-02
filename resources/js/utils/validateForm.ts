/**
 *
 * @param value value of the form field
 * @param rules validation logics to be applied
 * @returns errors returns in array format
 */ export default function validate(
    value: any,
    rules: Array<string>
  ): Array<string> {
    const errors: Array<string> = [];
    const isEmpty = value === undefined || value === "" || value === false;
    const ruleHandlers: Record<string, (rule: string) => void> = {
      required: () => {
        if (isEmpty) errors.push("This field is required!");
  
        if (rules.includes("array")) {
          if (value.length === 0) errors.push("This field is required");
        }
      },
      email: () => {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!regex.test(value)) errors.push("Please enter a valid email address");
      },
      mobile: () => {
        const regex = /^9\d{9}$/;
        if (!regex.test(value)) errors.push("Please enter a valid mobile number");
      },
      min: (rule) => {
        const minLength = parseInt(rule.split(":")[1], 10);
        if (value.length < minLength) {
          errors.push(`This field should have at least ${minLength} characters`);
        }
      },
      max: (rule) => {
        const maxLength = parseInt(rule.split(":")[1], 10);
        if (value.length > maxLength) {
          errors.push(
            `This field should not have more than ${maxLength} characters`
          );
        }
      },
    };
  
    rules?.forEach((rule) => {
      const [key] = rule.split(":");
      if (ruleHandlers[key]) {
        ruleHandlers[key](rule);
      }
    });
  
    return errors;
  }
  