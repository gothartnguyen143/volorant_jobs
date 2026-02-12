export class RequirementService {
  static async fetchRequirements() {
    try {
      const response = await fetch('/api/v1/cp-requirements');
      if (!response.ok) {
        throw new Error('Failed to fetch requirements');
      }
      const data = await response.json();
      return { requirements: data.requirements || [], selected: data.selected || {} };
    } catch (error) {
      console.error('Error fetching requirements:', error);
      return { requirements: [], selected: {} };
    }
  }
}