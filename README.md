# Moodle Block: OpenAI Chat Integration

## Overview

This Moodle plugin, `moodle-block_openai_chat`, allows instructors and students to interact with AI models through a chatbot interface directly within Moodle. Initially designed to work with OpenAI's API, this plugin will soon support integration with local AI models that follow the OpenAI standard. 

The plugin will provide a dynamic chat experience where responses from the AI are displayed in real-time, letter by letter, giving the user the feeling of a natural conversation with the AI.

### Current Features

- **Integration with OpenAI API**: Use OpenAI’s GPT-based models for chatting.
- **Real-time Typing Effect**: Responses from the AI are progressively displayed one letter at a time.
- **Easy Configuration**: Set your OpenAI API key and endpoint directly in the plugin settings.
- **Customizable Per-Block Configuration**: Choose between using OpenAI's API or a local AI system for each block instance.

## Features Coming Soon

The following features are planned for future releases of this plugin:

### 1. **Local AI Integration**
   - Connect to any local AI system that follows the OpenAI standard.
   - Users will be able to input their own local AI endpoint and API key, allowing for full control over the AI system.

### 2. **Per-Block AI Selection**
   - Users will have the flexibility to choose which AI (OpenAI or local) is used for each individual block instance in the course.
   - This will allow for greater customization and tailored learning experiences.

### 3. **Improved Chat Interface**
   - Add more customization options to the chat interface, such as themes, message history, and session storage.
   - Enhance the typing effect with customizable speeds and behaviors, allowing for a more immersive chat experience.

### 4. **Multi-language Support**
   - Provide support for AI models in different languages, expanding the reach of the plugin to non-English speaking users.
   - The language settings will allow for seamless communication with AI models in various languages.

### 5. **User Analytics and AI Response Tracking**
   - Introduce functionality to track and analyze user interactions with the AI chatbot, such as frequently asked questions, response accuracy, and engagement time.
   - Provide instructors with insights into how their students are interacting with the AI to improve course materials.

### 6. **Security Enhancements**
   - Future versions will focus on securing API keys and local AI endpoints, including encrypted storage and secure API communication.
   - Implement role-based access for controlling who can interact with the chatbot and view AI responses.

## Installation

1. Clone this repository to your Moodle server:

   ```bash
   git clone https://github.com/yourusername/moodle-block_openai_chat.git
   ```

2. Install the plugin in your Moodle `blocks` directory.
   
3. Log into Moodle as an administrator and navigate to the `Notifications` page to finish the installation.

4. Configure the plugin in the `Block settings` page to integrate OpenAI or your local AI system.

## Configuration

- After installation, go to the `Block settings` page for the plugin.
- Enter your **OpenAI API key** or configure the **Local AI endpoint** and **API key**.
- You can enable the **real-time typing effect** for a dynamic chat experience.

## Contributing

Contributions are welcome! If you would like to contribute to this project, feel free to submit a pull request. To ensure a smooth contribution process, please follow the guidelines below:

1. Fork the repository.
2. Create a new branch for your changes.
3. Submit your pull request for review.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

### Future Plans for Improvement

As this project evolves, the following milestones will be targeted:

- **Local AI Server Support**: Enable more flexible deployment options by integrating with various AI backends, expanding the scope of supported models.
- **Customizable Chat Features**: Enhance the chat experience with features such as conversation history, customizable themes, and enhanced UI components.
- **Advanced Analytics**: Provide administrators and instructors with deeper insights into how students are using the AI to improve their learning experience.
  
Stay tuned for updates, and feel free to contribute or raise issues if you encounter any problems.
