import React, { useState, useRef } from 'react';
import { StyleSheet, Text, View, PanResponder, Dimensions, Animated } from 'react-native';

const { width, height } = Dimensions.get('window');

interface Target {
  id: number;
  x: number;
  y: number;
  size: number;
  points: number;
}

export default function App() {
  const [score, setScore] = useState(0);
  const [targets, setTargets] = useState<Target[]>([]);
  const [discPosition, setDiscPosition] = useState({ x: width / 2, y: height - 100 });
  const [isThrowing, setIsThrowing] = useState(false);
  const discAnim = useRef(new Animated.ValueXY(discPosition)).current;

  // Generate random targets
  const generateTargets = () => {
    const newTargets: Target[] = [];
    for (let i = 0; i < 5; i++) {
      newTargets.push({
        id: i,
        x: Math.random() * (width - 60) + 30,
        y: Math.random() * (height - 200) + 50,
        size: 40,
        points: Math.floor(Math.random() * 10) + 5,
      });
    }
    setTargets(newTargets);
  };

  // Initialize game
  React.useEffect(() => {
    generateTargets();
  }, []);

  const panResponder = useRef(
    PanResponder.create({
      onStartShouldSetPanResponder: () => true,
      onPanResponderGrant: () => {
        setIsThrowing(true);
      },
      onPanResponderMove: (evt, gestureState) => {
        const newX = Math.max(0, Math.min(width - 40, discPosition.x + gestureState.dx));
        const newY = Math.max(0, Math.min(height - 40, discPosition.y + gestureState.dy));
        setDiscPosition({ x: newX, y: newY });
        discAnim.setValue({ x: newX, y: newY });
      },
      onPanResponderRelease: (evt, gestureState) => {
        setIsThrowing(false);
        // Check for hits
        targets.forEach(target => {
          const distance = Math.sqrt(
            Math.pow(discPosition.x + 20 - target.x, 2) +
            Math.pow(discPosition.y + 20 - target.y, 2)
          );
          if (distance < target.size / 2 + 20) {
            setScore(prev => prev + target.points);
            setTargets(prev => prev.filter(t => t.id !== target.id));
          }
        });
      },
    })
  );

  const resetGame = () => {
    setScore(0);
    setDiscPosition({ x: width / 2, y: height - 100 });
    discAnim.setValue({ x: width / 2, y: height - 100 });
    generateTargets();
  };

  return (
    <View style={styles.container}>
      <View style={styles.header}>
        <Text style={styles.score}>Score: {score}</Text>
        <Text style={styles.title}>DiscGame</Text>
      </View>

      <View style={styles.gameArea}>
        {targets.map(target => (
          <View
            key={target.id}
            style={[
              styles.target,
              {
                left: target.x - target.size / 2,
                top: target.y - target.size / 2,
                width: target.size,
                height: target.size,
              },
            ]}
          >
            <Text style={styles.targetText}>{target.points}</Text>
          </View>
        ))}

        <Animated.View
          style={[
            styles.disc,
            {
              left: discAnim.x,
              top: discAnim.y,
            },
          ]}
          {...panResponder.current.panHandlers}
        >
          <Text style={styles.discText}>🟡</Text>
        </Animated.View>
      </View>

      <View style={styles.controls}>
        <Text style={styles.instruction}>
          Drag the disc to hit targets!
        </Text>
        <Text style={styles.reset} onPress={resetGame}>
          Reset Game
        </Text>
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#87CEEB',
  },
  header: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    padding: 20,
    backgroundColor: '#4CAF50',
  },
  score: {
    fontSize: 20,
    fontWeight: 'bold',
    color: 'white',
  },
  title: {
    fontSize: 24,
    fontWeight: 'bold',
    color: 'white',
  },
  gameArea: {
    flex: 1,
    position: 'relative',
  },
  target: {
    position: 'absolute',
    backgroundColor: '#FF5722',
    borderRadius: 50,
    justifyContent: 'center',
    alignItems: 'center',
    borderWidth: 2,
    borderColor: '#D84315',
  },
  targetText: {
    color: 'white',
    fontWeight: 'bold',
    fontSize: 16,
  },
  disc: {
    position: 'absolute',
    width: 40,
    height: 40,
    justifyContent: 'center',
    alignItems: 'center',
  },
  discText: {
    fontSize: 30,
  },
  controls: {
    padding: 20,
    backgroundColor: '#4CAF50',
    alignItems: 'center',
  },
  instruction: {
    fontSize: 16,
    color: 'white',
    marginBottom: 10,
  },
  reset: {
    fontSize: 18,
    color: 'white',
    textDecorationLine: 'underline',
  },
});
