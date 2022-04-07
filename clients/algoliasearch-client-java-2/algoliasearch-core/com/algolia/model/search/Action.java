package com.algolia.model.search;

import com.google.gson.TypeAdapter;
import com.google.gson.annotations.JsonAdapter;
import com.google.gson.stream.JsonReader;
import com.google.gson.stream.JsonWriter;
import java.io.IOException;

/** type of operation. */
@JsonAdapter(Action.Adapter.class)
public enum Action {
  ADD_OBJECT("addObject"),

  UPDATE_OBJECT("updateObject"),

  PARTIAL_UPDATE_OBJECT("partialUpdateObject"),

  PARTIAL_UPDATE_OBJECT_NO_CREATE("partialUpdateObjectNoCreate"),

  DELETE_OBJECT("deleteObject"),

  DELETE("delete"),

  CLEAR("clear");

  private final String value;

  Action(String value) {
    this.value = value;
  }

  public String getValue() {
    return value;
  }

  @Override
  public String toString() {
    return String.valueOf(value);
  }

  public static Action fromValue(String value) {
    for (Action b : Action.values()) {
      if (b.value.equals(value)) {
        return b;
      }
    }
    throw new IllegalArgumentException("Unexpected value '" + value + "'");
  }

  public static class Adapter extends TypeAdapter<Action> {

    @Override
    public void write(final JsonWriter jsonWriter, final Action enumeration)
      throws IOException {
      jsonWriter.value(enumeration.getValue());
    }

    @Override
    public Action read(final JsonReader jsonReader) throws IOException {
      String value = jsonReader.nextString();
      return Action.fromValue(value);
    }
  }
}
