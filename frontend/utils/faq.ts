export interface FaqItem {
  question: string;
  answer: string;
}

export function parseFaqMarkdown(markdown: string): FaqItem[] {
  return markdown
    .replace(/<!--[\s\S]*?-->/g, "")
    .split(/^##\s+/m)
    .slice(1)
    .map((section) => {
      const [questionLine, ...answerLines] = section.trim().split("\n");

      return {
        question: questionLine.trim(),
        answer: answerLines
          .map((line) => line.trim())
          .filter(Boolean)
          .join(" ")
          .replace(/\s+/g, " "),
      };
    })
    .filter(({ question, answer }) => question.length > 0 && answer.length > 0);
}
